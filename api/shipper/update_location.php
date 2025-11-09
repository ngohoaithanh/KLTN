<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
include_once('../../config/auth_check.php');
function float_or($v, $def){ return isset($v) ? floatval($v) : $def; }
function int_or($v, $def){ return isset($v) ? intval($v) : $def; }

$shipper_id = int_or($_POST['shipper_id'] ?? $_GET['shipper_id'] ?? null, 0);
$lat        = float_or($_POST['lat'] ?? $_GET['lat'] ?? null, null);
$lng        = float_or($_POST['lng'] ?? $_GET['lng'] ?? null, null);
$status     = $_POST['status'] ?? $_GET['status'] ?? 'online';
$accuracy   = float_or($_POST['accuracy'] ?? $_GET['accuracy'] ?? null, null);
$speed      = float_or($_POST['speed'] ?? $_GET['speed'] ?? null, null);
$heading    = float_or($_POST['heading'] ?? $_GET['heading'] ?? null, null);

if ($shipper_id <= 0 || $lat === null || $lng === null) {
  echo json_encode(['success'=>false,'error'=>'Thiếu shipper_id/lat/lng']); exit;
}

$db = new clsKetNoi();
$conn = $db->moKetNoi();

$sql = "INSERT INTO shipper_locations (shipper_id, lat, lng, accuracy, speed, heading, status)
        VALUES (?,?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE 
          lat=VALUES(lat), lng=VALUES(lng),
          accuracy=VALUES(accuracy), speed=VALUES(speed), heading=VALUES(heading),
          status=VALUES(status), updated_at=CURRENT_TIMESTAMP";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iddddds', $shipper_id, $lat, $lng, $accuracy, $speed, $heading, $status);

if (!$stmt->execute()) {
  echo json_encode(['success'=>false,'error'=>$stmt->error]); 
  $stmt->close(); $db->dongKetNoi($conn); exit;
}

$stmt->close();
echo json_encode(['success'=>true, 'message'=>'Updated']);
$db->dongKetNoi($conn);