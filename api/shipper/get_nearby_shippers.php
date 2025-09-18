<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/database.php');

function respond($ok, $data = []) {
    echo json_encode($ok ? array_merge(['success'=>true], $data)
                         : array_merge(['success'=>false], $data));
    exit;
}

$db  = new clsKetNoi();
$conn = $db->moKetNoi();
if (!$conn) respond(false, ['error'=>'Không kết nối DB']);

$lat    = isset($_GET['lat'])    ? floatval($_GET['lat'])    : null;
$lng    = isset($_GET['lng'])    ? floatval($_GET['lng'])    : null;
$radius = isset($_GET['radius']) ? intval($_GET['radius'])   : 5000; // mét
$limit  = isset($_GET['limit'])  ? intval($_GET['limit'])    : 5;

if ($lat === null || $lng === null) respond(false, ['error'=>'Thiếu lat/lng']);
if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    respond(false, ['error'=>'lat/lng không hợp lệ']);
}
if ($radius <= 0) $radius = 5000;
if ($limit <= 0 || $limit > 100) $limit = 5;

// BBOX xấp xỉ: 1 độ ≈ 111 km
$delta = $radius / 111000.0;
$latMin = $lat - $delta;
$latMax = $lat + $delta;
$lngMin = $lng - $delta;
$lngMax = $lng + $delta;

// Haversine + bbox + lọc online & cập nhật gần đây
$sql = "
    SELECT
      s.shipper_id,
      s.lat,
      s.lng,
      s.status,
      s.updated_at,
      (6371000 * 2 * ASIN(SQRT(
          POWER(SIN(RADIANS(s.lat - ?)/2), 2) +
          COS(RADIANS(?)) * COS(RADIANS(s.lat)) *
          POWER(SIN(RADIANS(s.lng - ?)/2), 2)
      ))) AS distance_m
    FROM shipper_locations s
    JOIN users u ON u.ID = s.shipper_id
    WHERE u.Role = 6
      AND s.status = 'online'
      AND s.updated_at > NOW() - INTERVAL 5 MINUTE
      AND s.lat BETWEEN ? AND ?
      AND s.lng BETWEEN ? AND ?
    HAVING distance_m < ?
    ORDER BY distance_m ASC
    LIMIT ?
";

if (!($stmt = $conn->prepare($sql))) respond(false, ['error'=>$conn->error]);
/*
  Param order:
  1: lat (for distance)
  2: lat (for cos)
  3: lng (for distance)
  4: latMin
  5: latMax
  6: lngMin
  7: lngMax
  8: radius (meters)
  9: limit
*/
$stmt->bind_param(
    "dddddddi",
    $lat, $lat, $lng,
    $latMin, $latMax,
    $lngMin, $lngMax,
    $radius, $limit
);
if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();
    respond(false, ['error'=>$err]);
}
$res = $stmt->get_result();
$list = [];
while ($row = $res->fetch_assoc()) {
    $list[] = [
        'shipper_id' => intval($row['shipper_id']),
        'lat'        => floatval($row['lat']),
        'lng'        => floatval($row['lng']),
        'status'     => $row['status'],
        'updated_at' => $row['updated_at'],
        'distance_m' => round(floatval($row['distance_m']), 2)
    ];
}
$stmt->close();

respond(true, ['data'=>$list]);
