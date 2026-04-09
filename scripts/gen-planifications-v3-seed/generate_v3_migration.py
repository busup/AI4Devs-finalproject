#!/usr/bin/env python3
"""
Genera Flyway V3 para ms-planifications: expeditions + planifications + services
abril 2026 para snapshots de ms-router que NO están en V2 (las 3 primeras rutas BCN).

UUID v5 namespace alineado con comentarios del seed V2.
"""
from __future__ import annotations

import uuid
from datetime import timedelta
from pathlib import Path

NS = uuid.UUID("a1b2c3d4-e5f6-4789-a012-3456789abcde")


def uid(name: str) -> str:
    return str(uuid.uuid5(NS, name))


def add_seconds_to_time(base_hms: str, sec: int) -> str:
    h, m, s = map(int, base_hms.split(":"))
    t0 = timedelta(hours=h, minutes=m, seconds=s)
    t1 = t0 + timedelta(seconds=sec)
    total = int(t1.total_seconds()) % 86400
    hh = total // 3600
    mm = (total % 3600) // 60
    ss = total % 60
    return f"{hh:02d}:{mm:02d}:{ss:02d}"


# Snapshots en V2 (no repetir)
V2_SNAPSHOTS = {
    "e8224e54-8d58-5cc6-b078-3dd9c2a051d5",
    "1f85708b-9744-52d7-8fcc-65bbf09bb14b",
    "6c79ea21-8b50-5bca-963c-370c0583b5cb",
}

# Resto de rutas aprobadas en V2__bcn_routes_stops_seed.sql (ms-router)
ROUTES: list[dict] = [
    {
        "snapshot": "d9bcd967-3f49-50e4-a36b-121d38f11655",
        "label": "TEST BIANUAL IDA",
        "duration_s": 2375,
        "base": "09:00:00",
        "stops": [
            "d14f9257-139f-578b-add6-07ae271d3ba9",
            "d539bf1a-ba92-5689-8b14-cc79dc65c668",
        ],
    },
    {
        "snapshot": "3da69b87-3cb7-56c4-80f4-c6a0bf600c51",
        "label": "TEST BIANUAL VUELTA",
        "duration_s": 2280,
        "base": "09:10:00",
        "stops": [
            "34cae3fb-0786-55e1-ac3f-2f86e0494334",
            "a1219b57-64aa-5f42-a81e-4f61b3569ce2",
        ],
    },
    {
        "snapshot": "ef867ff2-20fc-5025-a256-79ab98efd335",
        "label": "TEST SERVICES LIKE AMAZON",
        "duration_s": 3296,
        "base": "09:20:00",
        "stops": [
            "ccc1f2f2-70e5-509a-8cce-fed6379b9311",
            "8e637ea2-74cb-5895-9e8c-c706e1a0daf6",
            "f7f5e5af-be85-5811-879a-2d2fe2bb3fe7",
            "8695ffe5-9c11-5823-a89c-1ff35c289fe8",
        ],
    },
    {
        "snapshot": "104b6ff7-0083-5c52-9467-d816747f643a",
        "label": "test stops",
        "duration_s": 2041,
        "base": "09:30:00",
        "stops": [
            "c4fc9b02-388b-5ee5-95a4-20452db90d70",
            "4879b30b-8640-5c28-ba55-e77c9ce4a6b3",
        ],
    },
    {
        "snapshot": "8e3abeb3-283a-56f3-bb90-f830d791557a",
        "label": "Ruta Planificada Base",
        "duration_s": 3418,
        "base": "09:40:00",
        "stops": [
            "6227f2f1-830d-52e3-8f49-955532712a45",
            "01c10ff8-b1c0-55fc-9050-2c58ad387002",
        ],
    },
    {
        "snapshot": "f81b9f59-26c3-58e8-a8ce-d8a15f2147b8",
        "label": "ITMT-227-test 1",
        "duration_s": 1904,
        "base": "09:50:00",
        "stops": [
            "13b5bbc7-fc92-5540-9437-8947663ff900",
            "004b15ab-cf2b-5cfa-be23-8f9cf0705f64",
            "420eeca3-c7aa-55f5-b875-0b11c67c54e7",
            "c20c763d-1025-5b48-ac00-72c48e59fa0f",
        ],
    },
    {
        "snapshot": "6309cb50-d196-51c4-b835-16becf30b2b4",
        "label": "test WL",
        "duration_s": 638,
        "base": "10:00:00",
        "stops": [
            "9f7c0264-8636-58ed-9186-660edc4bb005",
            "c6c8a4b0-0b92-55ce-89e8-1fab91e93cc3",
            "0d28793a-cf1e-5523-b7bd-c653078873f4",
            "411ae1f7-a84f-53b1-b873-5d7ccb231693",
        ],
    },
    {
        "snapshot": "3762cc2f-d311-580b-8de5-d39b684a8eab",
        "label": "test - 0809",
        "duration_s": 227,
        "base": "10:10:00",
        "stops": [
            "44aa867c-e4c7-5565-96bb-60a8fc99558c",
            "84346dc6-9832-5fbf-9e8f-693c2ef9652f",
        ],
    },
    {
        "snapshot": "6bf2ea98-6a5d-54ab-941f-95b1ed144f08",
        "label": "Ruta Tradicional Circular",
        "duration_s": 4218,
        "base": "10:20:00",
        "stops": [
            "28625012-ad71-5605-b2e9-2d599aafaa37",
            "af9667a8-780e-5e4c-b8fe-92972b6b8b36",
            "af3e9ae8-e512-537e-a970-08f958991686",
            "29d1d511-53be-5c95-83ea-30c62b6835c0",
        ],
    },
    {
        "snapshot": "69c1418b-0ed5-52c2-a7c5-fcfe71b802ee",
        "label": "TEST ITMT-511",
        "duration_s": 191,
        "base": "10:30:00",
        "stops": [
            "13c5cb0f-e2ea-5700-918b-c1eaf3668de2",
            "df4aa27d-df97-5e72-a31b-ce262b399bb6",
            "44b67add-b2ce-5961-883b-c555ce68ab55",
            "71eb0354-783f-5062-8bb1-65fc62177b94",
            "dbf1e82d-fb79-5df4-a581-a093306f6682",
            "7ce6488b-f358-526c-a394-0c89ffe9834b",
        ],
    },
    {
        "snapshot": "8811cc7b-3f25-57b2-8c54-4bdf723b2087",
        "label": "TEST ITMT-908",
        "duration_s": 261,
        "base": "10:40:00",
        "stops": [
            "b4f9b9cb-2dda-55bb-8bfa-164b2d474d36",
            "8463581f-f362-5f29-a6f9-129c0a630a04",
            "d12b51a4-5e1e-50ab-9bb0-33283d83c08c",
            "ddeb5247-63e7-567a-a531-f26d1a446405",
            "f025ba87-3260-5934-9b45-851894b08e26",
        ],
    },
]


def offsets_for_stops(duration_s: int, n: int) -> list[int]:
    if n <= 1:
        return [0]
    return [int(duration_s * i / (n - 1)) for i in range(n)]


def main() -> None:
    lines: list[str] = [
        "-- =============================================================",
        "-- ms-planifications | V3__seed_all_remaining_routes_april_2026.sql",
        "-- Expeditions + planifications + services (abr 2026) para todos los",
        "-- snapshots de ms-router V2 BCN seed que NO estaban en V2 planif.",
        "-- Generado por scripts/gen-planifications-v3-seed/generate_v3_migration.py",
        "-- UUID v5 namespace: a1b2c3d4-e5f6-4789-a012-3456789abcde",
        "-- =============================================================",
        "",
        "USE `ms-planifications`;",
        "",
        "SET NAMES utf8mb4;",
        "",
    ]

    for r in ROUTES:
        if r["snapshot"] in V2_SNAPSHOTS:
            continue
        snap = r["snapshot"]
        exp_id = uid(f"planif-v3:expedition:{snap}")
        pl_id = uid(f"planif-v3:planification:{snap}")
        stops = r["stops"]
        duration = r["duration_s"]
        base = r["base"]
        offsets = offsets_for_stops(duration, len(stops))

        lines.append(
            f"INSERT INTO expeditions (id, name, route_snapshot_ref_id, days_of_week, base_time, status, metadata, created_at, updated_at, deleted_at) "
            f"VALUES ('{exp_id}', 'Seed V3 {r['label']}', '{snap}', 127, '{base}', 'active', NULL, NOW(), NOW(), NULL);"
        )
        for i, stop_id in enumerate(stops):
            es_id = uid(f"planif-v3:es:{snap}:{i + 1}")
            lines.append(
                "INSERT INTO expedition_stops (id, expedition_id, stop_logical_id, sequence_order, offset_seconds, active, pickup_allowed, dropoff_allowed) "
                f"VALUES ('{es_id}', '{exp_id}', '{stop_id}', {i + 1}, {offsets[i]}, 1, 1, 1);"
            )
        lines.append(
            "INSERT INTO planifications (id, expedition_id, date_from, date_until, exceptions, non_working_days, status, created_at, updated_at) "
            f"VALUES ('{pl_id}', '{exp_id}', '2026-04-01', '2026-04-30', NULL, NULL, 'active', NOW(), NOW());"
        )

        for day in range(1, 31):
            d = f"2026-04-{day:02d}"
            svc_id = uid(f"planif-v3:service:{snap}:{d}")
            lines.append(
                "INSERT INTO services (id, planification_id, route_snapshot_ref_id, service_date, departure_time, capacity, status, created_at, updated_at) "
                f"VALUES ('{svc_id}', '{pl_id}', '{snap}', '{d}', '{base}', 50, 'scheduled', NOW(), NOW());"
            )
            for j, stop_id in enumerate(stops):
                ss_id = uid(f"planif-v3:ss:{svc_id}:{j + 1}")
                st = add_seconds_to_time(base, offsets[j])
                lines.append(
                    "INSERT INTO service_stops (id, service_id, stop_logical_id, sequence_order, scheduled_time, pickup_allowed, dropoff_allowed, active) "
                    f"VALUES ('{ss_id}', '{svc_id}', '{stop_id}', {j + 1}, '{st}', 1, 1, 1);"
                )
        lines.append("")

    # Totales esperados (11 rutas × 30 días = 330 servicios nuevos)
    lines.extend(
        [
            "-- Verificación:",
            "-- SELECT COUNT(*) FROM services WHERE service_date BETWEEN '2026-04-01' AND '2026-04-30';",
            "--   V2: 90 + V3: 330 = 420 (si ambas migraciones aplicadas en BD limpia).",
        ]
    )

    out = Path(__file__).resolve().parents[2] / "ms-planifications/src/main/resources/db/migration/V3__seed_all_remaining_routes_april_2026.sql"
    out.write_text("\n".join(lines) + "\n", encoding="utf-8")
    print(f"Wrote {out} ({len(lines)} lines)")


if __name__ == "__main__":
    main()
