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

$shipperId = isset($_GET['shipper_id']) ? intval($_GET['shipper_id']) : 0;
if ($shipperId <= 0) respond(false, ['error'=>'Thiếu shipper_id']);

$sql = "SELECT shipper_id, lat, lng, status, updated_at
        FROM shipper_locations
        WHERE shipper_id=?";
if (!($stmt = $conn->prepare($sql))) respond(false, ['error'=>$conn->error]);
$stmt->bind_param("i", $shipperId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$row) respond(false, ['error'=>'Không có dữ liệu']);

respond(true, [
    'shipper_id' => intval($row['shipper_id']),
    'lat'        => floatval($row['lat']),
    'lng'        => floatval($row['lng']),
    'status'     => $row['status'],
    'updated_at' => $row['updated_at']
]);
