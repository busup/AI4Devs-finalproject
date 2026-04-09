-- Verificación post-import BCN (T11) — ejecutar contra `ms-router` tras aplicar bcn_ms_router_seed.sql
-- Sustituir :import_tag si usas otro valor en import_bcn_routes.py (IMPORT_TAG).

SET NAMES utf8mb4;

-- Conteo de rutas importadas (ajusta el tag si cambió)
-- SELECT COUNT(*) AS routes_bcn FROM routes r
-- WHERE r.id IN (SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(payload, '$.route_id')) FROM outbox_events WHERE event_type = 'RoutePublished');

-- Rutas con snapshot actual definido
SELECT COUNT(*) AS routes_with_current_snapshot
FROM routes
WHERE current_snapshot_id IS NOT NULL;

-- Snapshots huérfanos (no debería haber)
SELECT COUNT(*) AS orphan_snapshots
FROM route_snapshots rs
LEFT JOIN routes r ON r.current_snapshot_id = rs.id
WHERE r.id IS NULL;

-- Paradas con metadata de import
SELECT COUNT(*) AS stops_with_import_tag
FROM stops
WHERE JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.import_tag')) = 'bcn-import-2026-04';

-- Por snapshot: número de route_stops (secuencia 1..N sin huecos — revisar manualmente si N>0)
SELECT rs.id AS snapshot_id, COUNT(*) AS n_stops, MIN(rs2.sequence_order) AS min_seq, MAX(rs2.sequence_order) AS max_seq
FROM route_snapshots rs
JOIN route_stops rs2 ON rs2.route_snapshot_id = rs.id
GROUP BY rs.id
HAVING min_seq <> 1 OR max_seq <> COUNT(*);

-- Distancia entre paradas consecutivas (m) — advertir si > 200_000 m salvo rutas especiales
SELECT rs.route_snapshot_id,
       rs.sequence_order,
       ST_Distance_Sphere(s1.location, s2.location) AS gap_m
FROM route_stops rs
JOIN stops s1 ON s1.id = rs.stop_id
JOIN route_stops rs_next
  ON rs_next.route_snapshot_id = rs.route_snapshot_id AND rs_next.sequence_order = rs.sequence_order + 1
JOIN stops s2 ON s2.id = rs_next.stop_id
WHERE ST_Distance_Sphere(s1.location, s2.location) > 200000
ORDER BY gap_m DESC
LIMIT 50;

-- Geometrías polyline presentes por snapshot
SELECT route_snapshot_id, COUNT(*) AS n_geom
FROM route_geometries
WHERE format = 'polyline' AND geometry_type = 'full'
GROUP BY route_snapshot_id;

-- Outbox pendiente de publicación (esperado published=0 tras seed)
SELECT COUNT(*) AS outbox_unpublished FROM outbox_events WHERE published = 0 AND event_type = 'RoutePublished';
