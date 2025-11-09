<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/database.php');
include_once('../../config/auth_check.php');
function respond($ok, $data = []) {
    echo json_encode($ok ? array_merge(['success'=>true], $data)
                         : array_merge(['success'=>false], $data));
    exit;
}

$db  = new clsKetNoi();
$conn = $db->moKetNoi();
if (!$conn) respond(false, ['error'=>'Không kết nối DB']);

$shipperId = isset($_POST['shipper_id']) ? intval($_POST['shipper_id']) : 0;
$lat       = isset($_POST['lat']) ? floatval($_POST['lat']) : null;
$lng       = isset($_POST['lng']) ? floatval($_POST['lng']) : null;
$status    = isset($_POST['status']) ? trim($_POST['status']) : 'online';

if ($shipperId <= 0 || $lat === null || $lng === null) {
    respond(false, ['error' => 'Thiếu dữ liệu: shipper_id, lat, lng']);
}
if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    respond(false, ['error' => 'lat/lng không hợp lệ']);
}

// Kiểm tra shipper tồn tại & đúng Role=6
$sqlCheck = "SELECT ID FROM users WHERE ID=? AND Role=6 LIMIT 1";
if (!($stmt = $conn->prepare($sqlCheck))) respond(false, ['error'=>$conn->error]);
$stmt->bind_param("i", $shipperId);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    respond(false, ['error' => 'Shipper không tồn tại hoặc không đúng role']);
}
$stmt->close();

// Upsert vị trí (1 dòng/shipper)
$sql = "INSERT INTO shipper_locations (shipper_id, lat, lng, status, updated_at)
        VALUES (?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
          lat=VALUES(lat),
          lng=VALUES(lng),
          status=VALUES(status),
          updated_at=NOW()";
if (!($stmt = $conn->prepare($sql))) respond(false, ['error'=>$conn->error]);
$stmt->bind_param("idds", $shipperId, $lat, $lng, $status);
if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();
    respond(false, ['error'=>$err]);
}
$stmt->close();

respond(true, ['message'=>'Cập nhật thành công']);
