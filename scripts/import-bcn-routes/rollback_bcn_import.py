#!/usr/bin/env python3
"""
Elimina datos del import BCN en ms-router usando el mapping JSON (T10 rollback).

Orden respeta FKs: outbox → geometrías → route_stops → snapshots (tras NULL en routes) → routes → stops.

Uso:
  python rollback_bcn_import.py                    # imprime SQL
  python rollback_bcn_import.py --apply            # ejecuta (pymysql + env MYSQL_*)

Mapping por defecto: datos/mappings/bcn_import_latest.json
"""
from __future__ import annotations

import argparse
import json
import os
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
DEFAULT_MAPPING = ROOT / "datos" / "mappings" / "bcn_import_latest.json"
IMPORT_TAG = "bcn-import-2026-04"


def load_mapping(path: Path) -> dict:
    return json.loads(path.read_text(encoding="utf-8"))


def build_sql(data: dict) -> str:
    route_ids = list(data.get("routes", {}).values())
    snap_ids = list(data.get("snapshots", {}).values())
    stop_ids = list(data.get("stops", {}).values())

    if not route_ids:
        return "-- Mapping vacío: nada que borrar.\n"

    def q(ids: list[str]) -> str:
        return ", ".join(f"'{x}'" for x in ids)

    lines = [
        "-- Rollback import BCN (generado por rollback_bcn_import.py)",
        "SET NAMES utf8mb4;",
        "SET FOREIGN_KEY_CHECKS=0;",
        f"UPDATE routes SET current_snapshot_id = NULL WHERE id IN ({q(route_ids)});",
        f"DELETE FROM outbox_events WHERE aggregate_id IN ({q(route_ids)});",
        f"DELETE FROM route_geometries WHERE route_snapshot_id IN ({q(snap_ids)});",
        f"DELETE FROM route_stops WHERE route_snapshot_id IN ({q(snap_ids)});",
        f"DELETE FROM route_snapshots WHERE id IN ({q(snap_ids)});",
        f"DELETE FROM routes WHERE id IN ({q(route_ids)});",
        f"DELETE FROM stops WHERE id IN ({q(stop_ids)}) AND JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.import_tag')) = '{IMPORT_TAG}';",
        "SET FOREIGN_KEY_CHECKS=1;",
    ]
    return "\n".join(lines) + "\n"


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
    ap.add_argument("--mapping", type=Path, default=DEFAULT_MAPPING, help="JSON de mapeo")
    ap.add_argument("--apply", action="store_true", help="Ejecutar en MySQL")
    args = ap.parse_args()

    if not args.mapping.exists():
        print(f"No existe {args.mapping}. Ejecuta primero import_bcn_routes.py.", file=sys.stderr)
        sys.exit(1)

    data = load_mapping(args.mapping)
    sql = build_sql(data)
    if args.apply:
        apply_mysql(sql)
        print("Rollback aplicado OK.")
    else:
        print(sql, end="")


if __name__ == "__main__":
    main()
