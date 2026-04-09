#!/usr/bin/env python3
"""
Import BCN routes/stops CSV → ms-router SQL + mapping JSON.
Uso:
  python import_bcn_routes.py --profile-only   # solo informe T1
  python import_bcn_routes.py                    # genera SQL + mapping (default)
  python import_bcn_routes.py --apply          # requiere pymysql + env MYSQL_*

Env: MYSQL_HOST, MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE (default ms-router)

Idempotencia (T10): los UUID son deterministas; un segundo --apply sin borrar antes
fallará por clave duplicada. Ejecuta rollback_bcn_import.py (o el SQL generado) y vuelve a importar.
"""

from __future__ import annotations

import argparse
import csv
import json
import os
import sys
import uuid
from collections import defaultdict
from dataclasses import dataclass, field
from datetime import datetime
from pathlib import Path
from typing import Any, Dict, List, Optional, Tuple

# Namespace UUID determinista para uuid5
NS = uuid.UUID("018f0e2a-8d97-7b3c-9f01-2b3c4d5e6f00")
IMPORT_TAG = "bcn-import-2026-04"

ROOT = Path(__file__).resolve().parents[2]
CSV_ROUTES = ROOT / "datos" / "rutasBCN.csv"
CSV_STOPS = ROOT / "datos" / "ParadasRutasBCN.csv"
OUT_DIR = Path(__file__).resolve().parent / "out"
DOCS_DIR = ROOT / "docs"
MAPPING_DIR = ROOT / "datos" / "mappings"
# Migración Flyway: `docker compose run --rm flyway-router` aplica V1 + este V2
FLYWAY_V2 = (
    ROOT
    / "ms-router"
    / "src"
    / "main"
    / "resources"
    / "db"
    / "migration"
    / "V2__bcn_routes_stops_seed.sql"
)


def flyway_v2_header() -> str:
    return f"""-- =============================================================
-- ms-router | V2__bcn_routes_stops_seed.sql
-- Datos BCN: routes, stops, route_snapshots, route_stops, route_geometries, outbox_events.
-- import_tag: {IMPORT_TAG}
-- Regenerar: python3 scripts/import-bcn-routes/import_bcn_routes.py
-- Fuentes: datos/rutasBCN.csv, datos/ParadasRutasBCN.csv
-- =============================================================
USE `ms-router`;

"""


def sql_body_for_flyway(sql_lines: List[str]) -> str:
    """Quita el banner del generador (primeras líneas --) y concatena el SQL ejecutable."""
    i = 0
    while i < len(sql_lines) and sql_lines[i].strip().startswith("--"):
        i += 1
    return "\n".join(sql_lines[i:]) + "\n"

sys.path.insert(0, str(Path(__file__).resolve().parent))
from polyline_codec import decode as poly_decode
from polyline_codec import path_length_m


def canonical_encoded_polyline(encoded: str) -> Tuple[str, int]:
    """Devuelve (string codificado utilizable, distancia aprox. en m). Intenta recortar el final si el CSV trae basura."""
    best = ("", 0)
    for trim in range(0, 6):
        s = encoded[:-trim] if trim else encoded
        if len(s) < 10:
            continue
        try:
            coords = poly_decode(s)
            d = int(path_length_m(coords))
            return (s, max(d, 1))
        except Exception:
            continue
    return best


def uuid5(name: str) -> str:
    return str(uuid.uuid5(NS, name))


def in_spain_bbox(lat: float, lon: float) -> bool:
    return 36.0 <= lat <= 44.0 and -10.0 <= lon <= 5.0


def parse_polyline_field(raw: str) -> Optional[str]:
    """Devuelve solo el string codificado Google (sin corchetes/comillas envolventes).

    El CSV real termina en `...M"]` (abre `[`, cierra con `"]` como si fuera JSON truncado).
    """
    raw = (raw or "").strip()
    if not raw:
        return None
    try:
        data = json.loads(raw)
        if isinstance(data, list) and data and isinstance(data[0], str):
            return data[0].strip()
    except json.JSONDecodeError:
        pass
    if raw.startswith("["):
        raw = raw[1:]
    if '"]' in raw:
        raw = raw.rsplit('"]', 1)[0]
    raw = raw.strip()
    return raw if len(raw) > 10 else None


def load_routes() -> List[Dict[str, Any]]:
    rows = []
    with CSV_ROUTES.open(encoding="utf-8", newline="") as f:
        r = csv.DictReader(f)
        for row in r:
            rid = int(row["id"])
            poly = parse_polyline_field(row.get("polyline_raw") or "")
            rows.append(
                {
                    "legacy_id": rid,
                    "title": (row.get("title") or "").strip() or f"Ruta {rid}",
                    "polyline_encoded": poly,
                }
            )
    return rows


def load_stops_rows() -> List[Dict[str, Any]]:
    rows = []
    with CSV_STOPS.open(encoding="utf-8", newline="") as f:
        r = csv.DictReader(f)
        for row in r:
            try:
                legacy_stop_id = int(row["id"])
                route_id = int(row["route_id"])
            except (KeyError, ValueError):
                continue
            try:
                lat = float(row["lat"])
                lng = float(row["lng"])
            except (KeyError, ValueError):
                continue
            st_raw = row.get("stop_type_id") or ""
            try:
                stop_type_id = int(st_raw) if st_raw not in ("", "NULL") else None
            except ValueError:
                stop_type_id = None

            ts = None
            for col in ("timestamp", "start_timestamp"):
                v = row.get(col) or ""
                if v:
                    try:
                        ts = datetime.strptime(v.strip()[:19], "%Y-%m-%d %H:%M:%S")
                        break
                    except ValueError:
                        pass

            rows.append(
                {
                    "legacy_stop_id": legacy_stop_id,
                    "route_id": route_id,
                    "stop_type_id": stop_type_id,
                    "lat": lat,
                    "lng": lng,
                    "title": (row.get("title") or "").strip(),
                    "known_title": (row.get("known_title") or "").strip(),
                    "requested_address": (row.get("requested_address") or "").strip(),
                    "timestamp": ts,
                }
            )
    return rows


def profile(routes: List[Dict], stops: List[Dict]) -> str:
    route_ids = {r["legacy_id"] for r in routes}
    stop_by_route: Dict[int, List] = defaultdict(list)
    for s in stops:
        stop_by_route[s["route_id"]].append(s)

    orphan_routes = sorted(route_ids - set(stop_by_route.keys()))
    orphan_stops = sorted(set(stop_by_route.keys()) - route_ids)

    type_hist: Dict[str, int] = defaultdict(int)
    for s in stops:
        k = str(s["stop_type_id"]) if s["stop_type_id"] is not None else "NULL"
        type_hist[k] += 1

    outside = [s for s in stops if not in_spain_bbox(s["lat"], s["lng"])]

    lines = [
        "# Informe de perfilado CSV (T1) — rutas BCN",
        "",
        f"- **Rutas en `rutasBCN.csv`:** {len(routes)}",
        f"- **Filas en `ParadasRutasBCN.csv`:** {len(stops)}",
        f"- **`route_id` en paradas sin ruta en CSV de rutas:** {len(orphan_stops)} → {orphan_stops[:20]}{'…' if len(orphan_stops) > 20 else ''}",
        f"- **Rutas sin ninguna fila de paradas:** {len(orphan_routes)} → {orphan_routes[:20]}{'…' if len(orphan_routes) > 20 else ''}",
        "",
        "## Distribución `stop_type_id` (todas las filas)",
        "",
        "| Valor | Filas |",
        "|-------|-------|",
    ]
    for k in sorted(type_hist.keys(), key=lambda x: (x == "NULL", x)):
        lines.append(f"| {k} | {type_hist[k]} |")

    lines.extend(
        [
            "",
            "## Coordenadas fuera del bbox España (~import)",
            "",
            f"- Filas con lat/lng fuera de [36–44] × [-10–5]: **{len(outside)}**",
        ]
    )
    if outside[:15]:
        lines.append("")
        lines.append("| legacy_stop_id | route_id | lat | lng |")
        lines.append("|----------------|----------|-----|-----|")
        for s in outside[:15]:
            lines.append(
                f"| {s['legacy_stop_id']} | {s['route_id']} | {s['lat']} | {s['lng']} |"
            )

    lines.extend(
        [
            "",
            "### Decisión",
            "",
            "Se **excluyen** filas de paradas fuera del bbox para el seed ms-router. Rutas que queden sin paradas válidas (tras excluir `stop_type_id=100` y fuera de bbox) **no se importan**.",
            "",
            "### Encoding",
            "",
            "Ficheros leídos como **UTF-8**. Parser: `csv.DictReader` (respeta comillas y polilíneas largas).",
        ]
    )
    return "\n".join(lines) + "\n"


@dataclass
class BuildResult:
    sql_lines: List[str] = field(default_factory=list)
    mapping: Dict[str, Any] = field(default_factory=dict)
    skipped_routes: List[str] = field(default_factory=list)
    geometry_failures: List[str] = field(default_factory=list)


def build_sql_and_mapping(
    routes: List[Dict], stops: List[Dict]
) -> BuildResult:
    br = BuildResult()
    mapping: Dict[str, Any] = {
        "import_tag": IMPORT_TAG,
        "routes": {},
        "stops": {},
        "snapshots": {},
        "route_stops": [],
    }

    # Agrupar paradas por route_id, filtrar bbox y tipo 100
    by_route: Dict[int, List[Dict]] = defaultdict(list)
    for s in stops:
        if not in_spain_bbox(s["lat"], s["lng"]):
            continue
        if s["stop_type_id"] == 100:
            continue
        by_route[s["route_id"]].append(s)

    for rid in by_route:
        by_route[rid].sort(key=lambda x: (x["timestamp"] or datetime.min, x["legacy_stop_id"]))

    sql: List[str] = [
        "-- Generated by import_bcn_routes.py — ms-router seed",
        f"-- import_tag: {IMPORT_TAG}",
        "SET NAMES utf8mb4;",
        "SET FOREIGN_KEY_CHECKS=0;",
        "",
    ]

    for route in routes:
        lid = route["legacy_id"]
        if lid not in by_route or not by_route[lid]:
            br.skipped_routes.append(f"{lid}: sin paradas válidas tras filtros")
            continue

        route_uuid = uuid5(f"route-searcher/bcn-import:routes:{lid}")
        snap_uuid = uuid5(f"route-searcher/bcn-import:snapshot:v1:{lid}")
        mapping["routes"][str(lid)] = route_uuid
        mapping["snapshots"][str(lid)] = snap_uuid

        title = route["title"].replace("'", "''")[:255]
        raw_poly = route["polyline_encoded"]
        dist_m, dur_s = 1, 3600
        geom_encoded = ""
        if raw_poly:
            geom_encoded, dist_m = canonical_encoded_polyline(raw_poly)
            if dist_m <= 1:
                br.geometry_failures.append(str(lid))
            dur_s = max(int(dist_m / 9.72), 60) if dist_m > 1 else 3600
        else:
            br.geometry_failures.append(str(lid))

        sql.append(
            f"INSERT INTO routes (id, name, status, current_snapshot_id) VALUES "
            f"('{route_uuid}', '{title}', 'approved', NULL);"
        )
        sql.append(
            f"INSERT INTO route_snapshots (id, route_id, version_number, total_distance_m, estimated_duration_s, "
            f"valid_from, valid_until, published_at) VALUES ("
            f"'{snap_uuid}', '{route_uuid}', 1, {max(dist_m, 1)}, {max(dur_s, 60)}, "
            f"'2020-01-01', '2030-12-31', NOW());"
        )
        sql.append(
            f"UPDATE routes SET current_snapshot_id = '{snap_uuid}' WHERE id = '{route_uuid}';"
        )

        seq = 0
        for s in by_route[lid]:
            seq += 1
            stop_uuid = uuid5(f"route-searcher/bcn-import:stops:{s['legacy_stop_id']}")
            mapping["stops"][str(s["legacy_stop_id"])] = stop_uuid

            name = s["known_title"] or s["title"] or s["requested_address"] or f"Parada {s['legacy_stop_id']}"
            name = name.replace("''", "'")[:255]
            addr = (s["requested_address"] or "").replace("''", "'")[:500]
            meta = json.dumps(
                {
                    "import_tag": IMPORT_TAG,
                    "legacy_stop_id": s["legacy_stop_id"],
                    "legacy_route_id": lid,
                    "stop_type_id": s["stop_type_id"],
                },
                ensure_ascii=False,
            )
            meta_sql = meta.replace("\\", "\\\\").replace("'", "''")

            def esc_sql(t: str) -> str:
                return t.replace("\\", "\\\\").replace("'", "''")

            lat, lon = s["lat"], s["lng"]
            addr_sql = f"'{esc_sql(addr)}'" if addr else "NULL"
            sql.append(
                f"INSERT INTO stops (id, name, address, lat, lon, location, timezone, is_accessible, approval_status, metadata) VALUES ("
                f"'{stop_uuid}', '{esc_sql(name)}', {addr_sql}, "
                f"{lat:.7f}, {lon:.7f}, ST_SRID(POINT({lon:.7f}, {lat:.7f}), 4326), "
                f"'Europe/Madrid', 0, 'approved', CAST('{meta_sql}' AS JSON));"
            )

            dwell = 0
            rs_uuid = uuid5(f"route-searcher/bcn-import:route_stop:{lid}:{s['legacy_stop_id']}")
            if s["title"]:
                _alias = s["title"].replace("''", "'")
                alias_sql = f"'{esc_sql(_alias)[:255]}'"
            else:
                alias_sql = "NULL"
            sql.append(
                f"INSERT INTO route_stops (id, route_snapshot_id, stop_id, sequence_order, dwell_time_s, alias, pickup_allowed, dropoff_allowed, active) VALUES ("
                f"'{rs_uuid}', '{snap_uuid}', '{stop_uuid}', {seq}, {dwell}, {alias_sql}, 1, 1, 1);"
            )
            mapping["route_stops"].append(
                {"legacy_route_id": lid, "legacy_stop_id": s["legacy_stop_id"], "sequence": seq}
            )

        if geom_encoded:
            content = geom_encoded.replace("\\", "\\\\").replace("'", "''")
            rg_uuid = uuid5(f"route-searcher/bcn-import:geometry:{lid}")
            sql.append(
                f"INSERT INTO route_geometries (id, route_snapshot_id, geometry_type, format, content) VALUES ("
                f"'{rg_uuid}', '{snap_uuid}', 'full', 'polyline', '{content}');"
            )

        payload = json.dumps(
            {
                "route_id": route_uuid,
                "snapshot_id": snap_uuid,
                "version_number": 1,
                "import_tag": IMPORT_TAG,
                "legacy_route_id": lid,
            },
            ensure_ascii=False,
        ).replace("'", "''")
        ob_uuid = uuid5(f"route-searcher/bcn-import:outbox:{lid}")
        sql.append(
            f"INSERT INTO outbox_events (id, event_type, aggregate_type, aggregate_id, payload, published) VALUES ("
            f"'{ob_uuid}', 'RoutePublished', 'Route', '{route_uuid}', CAST('{payload}' AS JSON), 0);"
        )

    sql.append("SET FOREIGN_KEY_CHECKS=1;")
    br.sql_lines = sql
    br.mapping = mapping
    return br


def write_files(br: BuildResult, profile_md: str) -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    MAPPING_DIR.mkdir(parents=True, exist_ok=True)
    FLYWAY_V2.parent.mkdir(parents=True, exist_ok=True)
    (OUT_DIR / "bcn_ms_router_seed.sql").write_text("\n".join(br.sql_lines) + "\n", encoding="utf-8")
    FLYWAY_V2.write_text(flyway_v2_header() + sql_body_for_flyway(br.sql_lines), encoding="utf-8")
    (MAPPING_DIR / "bcn_import_latest.json").write_text(
        json.dumps(br.mapping, indent=2, ensure_ascii=False), encoding="utf-8"
    )
    (DOCS_DIR / "informe_perfilado_csv_bcn.md").write_text(profile_md, encoding="utf-8")
    (OUT_DIR / "import_summary.txt").write_text(
        "\n".join(
            ["Skipped routes:"]
            + br.skipped_routes
            + ["", "Geometry warnings / sin polyline:", ", ".join(br.geometry_failures) or "(ninguno)"]
        ),
        encoding="utf-8",
    )


def apply_mysql(sql_text: str) -> None:
    try:
        import pymysql
        from pymysql.constants import CLIENT
    except ImportError:
        print("Instala pymysql: pip install -r requirements.txt", file=sys.stderr)
        sys.exit(1)

    conn = pymysql.connect(
        host=os.environ.get("MYSQL_HOST", "127.0.0.1"),
        port=int(os.environ.get("MYSQL_PORT", "3306")),
        user=os.environ.get("MYSQL_USER", "root"),
        password=os.environ.get("MYSQL_PASSWORD", ""),
        database=os.environ.get("MYSQL_DATABASE", "ms-router"),
        charset="utf8mb4",
        client_flag=CLIENT.MULTI_STATEMENTS,
    )
    try:
        with conn.cursor() as cur:
            cur.execute(sql_text)
        conn.commit()
    finally:
        conn.close()


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--profile-only", action="store_true")
    ap.add_argument("--apply", action="store_true", help="Ejecutar SQL contra MySQL (requiere pymysql)")
    args = ap.parse_args()

    routes = load_routes()
    stops = load_stops_rows()
    profile_md = profile(routes, stops)

    if args.profile_only:
        DOCS_DIR.mkdir(parents=True, exist_ok=True)
        (DOCS_DIR / "informe_perfilado_csv_bcn.md").write_text(profile_md, encoding="utf-8")
        print(profile_md)
        return

    br = build_sql_and_mapping(routes, stops)
    write_files(br, profile_md)

    sql_text = "\n".join(br.sql_lines)
    print(f"SQL escrito: {OUT_DIR / 'bcn_ms_router_seed.sql'}")
    print(f"Flyway V2:   {FLYWAY_V2}")
    print(f"Mapping: {MAPPING_DIR / 'bcn_import_latest.json'}")
    print(f"Informe T1: {DOCS_DIR / 'informe_perfilado_csv_bcn.md'}")

    if args.apply:
        apply_mysql(sql_text)
        print("Aplicado a MySQL OK.")
    else:
        print("Dry-run: no se ha conectado a MySQL. Usa --apply con credenciales.")


if __name__ == "__main__":
    main()
