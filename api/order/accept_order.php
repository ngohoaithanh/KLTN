<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

/**
 * accept_order.php (HARD BLOCK)
 *
 * Đồng bộ với get_nearby_orders.php:
 * - Cùng hằng số fairness & feasibility.
 * - Chặn cứng nếu vi phạm tiêu chí ghép tuyến (xa_pickup / detour_lon).
 * - Dùng Accepted_at cho cooldown.
 */

const ONLINE_FRESH_SECONDS       = 60;
const MAX_CONCURRENT_ORDERS      = 2;
const COOLDOWN_SECONDS           = 10;

// Hard-block feasibility khi nhận
const ENFORCE_FEASIBILITY        = true;
const SECOND_ORDER_MAX_PICKUP_KM = 4.0;  // target→pickup đơn mới (km)
const MAX_ADDED_DETOUR_KM        = 5.0;  // “độ vòng thêm” (km)

/* ------------ Helpers ------------ */
function int_or($v, $def){ return isset($v) ? intval($v) : $def; }
function haversine_km($lat1,$lng1,$lat2,$lng2){
  $R=6371; $dLat=deg2rad($lat2-$lat1); $dLng=deg2rad($lng2-$lng1);
  $a=sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)**2;
  return 2*$R*asin(min(1,sqrt($a)));
}

/* ------------ Input ------------ */
$order_id   = int_or($_POST['order_id'] ?? $_GET['order_id'] ?? null, 0);
$shipper_id = int_or($_POST['shipper_id'] ?? $_GET['shipper_id'] ?? null, 0);
if ($order_id <= 0 || $shipper_id <= 0) {
  echo json_encode(['success'=>false, 'error'=>'Thiếu order_id/shipper_id']); exit;
}

/* ------------ DB & Transaction ------------ */
$db = new clsKetNoi();
$conn = $db->moKetNoi();
$conn->begin_transaction();

try {
  /* 1) Khóa đơn & kiểm tra trạng thái */
  $curStatus=null; $pickLat=null; $pickLng=null; $delLat=null; $delLng=null;
  $s = $conn->prepare("
    SELECT Status, Pick_up_lat, Pick_up_lng, Delivery_lat, Delivery_lng
    FROM orders
    WHERE ID=? FOR UPDATE
  ");
  $s->bind_param('i', $order_id);
  $s->execute();
  $s->bind_result($curStatus,$pickLat,$pickLng,$delLat,$delLng);
  $hasRow = $s->fetch();
  $s->close();

  if (!$hasRow) {
    $conn->rollback(); echo json_encode(['success'=>false,'error'=>'Đơn không tồn tại']); $db->dongKetNoi($conn); exit;
  }
  if ($curStatus !== 'pending') {
    $conn->rollback(); echo json_encode(['success'=>false,'error'=>'Đơn không còn pending']); $db->dongKetNoi($conn); exit;
  }

  /* 2) Shipper online + vị trí “fresh” */
  $status=null; $updatedAt=null; $curLat=null; $curLng=null;
  $q = $conn->prepare("SELECT status, updated_at, lat, lng FROM shipper_locations WHERE shipper_id=?");
  $q->bind_param('i',$shipper_id);
  $q->execute(); $q->bind_result($status,$updatedAt,$curLat,$curLng); $q->fetch(); $q->close();

  if ($status !== 'online' || $updatedAt===null) {
    $conn->rollback(); echo json_encode(['success'=>false,'error'=>'Shipper offline/không có vị trí']); $db->dongKetNoi($conn); exit;
  }
  $q2 = $conn->prepare("SELECT TIMESTAMPDIFF(SECOND, ?, NOW()) AS secs");
  $q2->bind_param('s',$updatedAt); $q2->execute();
  $diff = $q2->get_result()->fetch_assoc(); $q2->close();
  if (!$diff || intval($diff['secs']) > ONLINE_FRESH_SECONDS) {
    $conn->rollback(); echo json_encode(['success'=>false,'error'=>'Vị trí shipper không còn mới']); $db->dongKetNoi($conn); exit;
  }

  /* 3) Giới hạn số đơn active */
  $active=0;
  $q3=$conn->prepare("SELECT COUNT(*) FROM orders WHERE ShipperID=? AND Status IN ('accepted','picked_up','in_transit')");
  $q3->bind_param('i',$shipper_id); $q3->execute(); $q3->bind_result($active); $q3->fetch(); $q3->close();
  if ($active >= MAX_CONCURRENT_ORDERS) {
    $conn->rollback(); echo json_encode(['success'=>false,'error'=>'Đã đạt số đơn tối đa']); $db->dongKetNoi($conn); exit;
  }

  /* 4) Cooldown (theo Accepted_at) */
  $secs=null;
  $q4=$conn->prepare("
    SELECT TIMESTAMPDIFF(SECOND, MAX(Accepted_at), NOW())
    FROM orders
    WHERE ShipperID=? AND Status IN ('accepted','picked_up','in_transit')
  ");
  $q4->bind_param('i',$shipper_id); $q4->execute(); $q4->bind_result($secs); $q4->fetch(); $q4->close();
  if ($secs!==null && intval($secs) < COOLDOWN_SECONDS) {
    $remain = COOLDOWN_SECONDS - intval($secs);
    $conn->rollback(); echo json_encode(['success'=>false,'error'=>"Cooldown còn {$remain}s"]); $db->dongKetNoi($conn); exit;
  }

  /* 5) Feasibility hard-block (đồng bộ với list) */
  if (ENFORCE_FEASIBILITY) {
    // Lấy target hiện tại: đơn active gần nhất (ưu tiên cùng cách với list)
    $tLat=null; $tLng=null;
    $qActive = $conn->prepare("
      SELECT COALESCE(Pick_up_lat, Delivery_lat) AS tlat,
             COALESCE(Pick_up_lng, Delivery_lng) AS tlng
      FROM orders
      WHERE ShipperID=? AND Status IN ('accepted','picked_up','in_transit')
      ORDER BY Accepted_at DESC
      LIMIT 1
    ");
    $qActive->bind_param('i',$shipper_id);
    $qActive->execute();
    $rActive = $qActive->get_result()->fetch_assoc();
    $qActive->close();
    if ($rActive) { $tLat = $rActive['tlat']; $tLng = $rActive['tlng']; }

    // Tọa độ pickup ưu tiên; nếu thiếu dùng delivery
    $targetLat = ($pickLat!==null) ? floatval($pickLat) : (($delLat!==null) ? floatval($delLat) : null);
    $targetLng = ($pickLng!==null) ? floatval($pickLng) : (($delLng!==null) ? floatval($delLng) : null);

    // Chỉ kiểm tra khi đủ dữ liệu vị trí
    if ($tLat!==null && $tLng!==null && $curLat!==null && $curLng!==null && $targetLat!==null && $targetLng!==null) {
      // (a) target→pickup mới
      $dTarget2New_km = haversine_km(floatval($tLat), floatval($tLng), $targetLat, $targetLng);
      if ($dTarget2New_km > SECOND_ORDER_MAX_PICKUP_KM) {
        $error_msg = "Pickup quá xa so với tuyến hiện tại (Target→Pickup: ".round($dTarget2New_km, 2)."km > ".SECOND_ORDER_MAX_PICKUP_KM."km)";
        $conn->rollback(); echo json_encode(['success'=>false,'error'=>$error_msg]); $db->dongKetNoi($conn); exit;
        // $conn->rollback(); echo json_encode(['success'=>false,'error'=>'Pickup quá xa so với tuyến hiện tại']); $db->dongKetNoi($conn); exit;
      }

      // (b) độ vòng thêm xấp xỉ: (shipper→pickup + pickup→target) − (shipper→target)
      $base_km    = haversine_km($curLat,$curLng, floatval($tLat), floatval($tLng));
      $withNew_km = haversine_km($curLat,$curLng, $targetLat,$targetLng)
                  + haversine_km($targetLat,$targetLng, floatval($tLat), floatval($tLng));
      $added_km   = max(0.0, $withNew_km - $base_km);
      if ($added_km > MAX_ADDED_DETOUR_KM) {
        $error_msg = "Độ vòng thêm quá lớn (".round($added_km, 2)."km > ".MAX_ADDED_DETOUR_KM."km)";
        $conn->rollback(); echo json_encode(['success'=>false,'error'=>$error_msg]); $db->dongKetNoi($conn); exit;
        // $conn->rollback(); echo json_encode(['success'=>false,'error'=>'Độ vòng thêm quá lớn']); $db->dongKetNoi($conn); exit;
      }
    }
    // Nếu không có đơn active (không có tLat/tLng) → đây là đơn đầu: bỏ qua 2 tiêu chí ghép tuyến.
  }

  /* 6) Gán shipper & chuyển 'accepted' (race-safe) */
  $u = $conn->prepare("UPDATE orders SET ShipperID=?, Status='accepted', Accepted_at=NOW() WHERE ID=? AND Status='pending'");
  $u->bind_param('ii',$shipper_id,$order_id);
  if (!$u->execute() || $u->affected_rows===0) {
    $u->close(); $conn->rollback();
    echo json_encode(['success'=>false,'error'=>'Nhận đơn thất bại (có thể bị nhận trước)']); $db->dongKetNoi($conn); exit;
  }
  $u->close();

  /* 7) Tracking (tuỳ chọn) */
  $msg = 'Shipper '.$shipper_id.' đã nhận đơn.';
  $t = $conn->prepare("INSERT INTO trackings (OrderID, Status, Updated_at) VALUES (?, ?, NOW())");
  $t->bind_param('is', $order_id, $msg);
  $t->execute(); $t->close();

  /* 8) Commit */
  $conn->commit();
  echo json_encode(['success'=>true,'message'=>'Đã nhận đơn']);

} catch (Exception $e) {
  $conn->rollback();
  echo json_encode(['success'=>false,'error'=>'Lỗi: '.$e->getMessage()]);
} finally {
  $db->dongKetNoi($conn);
}
