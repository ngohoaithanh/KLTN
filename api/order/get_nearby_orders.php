<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
include_once('../../config/auth_check.php');
/** ====== Feasibility hint (chỉ cảnh báo, không chặn) ====== */
const SECOND_ORDER_MAX_PICKUP_KM = 3.0;  // khoảng cách target→pickup đơn mới cho phép
// const MAX_HEADING_DIFF_DEG       = 60.0; // lệch hướng tối đa cho phép
const MAX_ADDED_DETOUR_KM        = 4.0;  // “độ vòng” tối đa cho phép

function haversine_km($lat1,$lng1,$lat2,$lng2){
  $R=6371; $dLat=deg2rad($lat2-$lat1); $dLng=deg2rad($lng2-$lng1);
  $a=sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)**2;
  return 2*$R*asin(min(1,sqrt($a)));
}
function bearing_deg($lat1,$lng1,$lat2,$lng2){
  $lat1=deg2rad($lat1); $lat2=deg2rad($lat2); $dLng=deg2rad($lng2-$lng1);
  $y=sin($dLng)*cos($lat2);
  $x=cos($lat1)*sin($lat2)-sin($lat1)*cos($lat2)*cos($dLng);
  $brng=rad2deg(atan2($y,$x));
  return fmod(($brng+360.0),360.0);
}
function heading_diff_deg($a,$b){
  $d=abs($a-$b); return ($d>180.0) ? 360.0-$d : $d;
}

/** ====== Fairness & rating ====== */
const ONLINE_FRESH_SECONDS   = 60;
const MAX_CONCURRENT_ORDERS  = 2;
const COOLDOWN_SECONDS       = 10;

const TIER_EXCELLENT_MIN     = 4.60;
const TIER_GOOD_MIN          = 4.00;
const TIER_WARN_MIN          = 3.00;
const HARD_BLOCK_MIN         = 3.00;
const USE_RATING_BOOSTS      = true;

const DEFAULT_NEW_SHIPPER_RATING = 4.50;

function float_or($v,$def){ return isset($v) ? floatval($v) : $def; }
function int_or($v,$def){ return isset($v) ? intval($v) : $def; }

/** ====== Input ====== */
$shipper_id = int_or($_GET['shipper_id'] ?? $_POST['shipper_id'] ?? null, 0);
$lat        = float_or($_GET['lat'] ?? $_POST['lat'] ?? null, null);
$lng        = float_or($_GET['lng'] ?? $_POST['lng'] ?? null, null);
$radius     = int_or($_GET['radius'] ?? $_POST['radius'] ?? null, 5000);
$limit      = int_or($_GET['limit']  ?? $_POST['limit']  ?? null, 10);

if ($shipper_id <= 0 || $lat === null || $lng === null) {
  echo json_encode(['success'=>false,'error'=>'Thiếu shipper_id/lat/lng']); exit;
}

$db = new clsKetNoi();
$conn = $db->moKetNoi();

/** 1) Online + vị trí “fresh” */
$st = null; $updatedAt = null;
$qs = $conn->prepare("SELECT status, updated_at FROM shipper_locations WHERE shipper_id=?");
$qs->bind_param('i',$shipper_id);
$qs->execute(); $qs->bind_result($st,$updatedAt); $qs->fetch(); $qs->close();

if ($st !== 'online' || $updatedAt === null) {
  echo json_encode(['success'=>true,'orders'=>[], 'info'=>'offline']); 
  $db->dongKetNoi($conn); exit;
}
$qs2 = $conn->prepare("SELECT TIMESTAMPDIFF(SECOND, ?, NOW()) AS secs");
$qs2->bind_param('s',$updatedAt);
$qs2->execute(); $diffRow = $qs2->get_result()->fetch_assoc(); $qs2->close();
if (!$diffRow || intval($diffRow['secs']) > ONLINE_FRESH_SECONDS) {
  echo json_encode(['success'=>true,'orders'=>[], 'info'=>'offline_or_stale']);
  $db->dongKetNoi($conn); exit;
}

/** 2) Đếm đơn active (KHÔNG early return) */
$active_count = 0;
$q1 = $conn->prepare("SELECT COUNT(*) FROM orders WHERE ShipperID=? AND Status IN ('accepted','picked_up','in_transit')");
$q1->bind_param('i',$shipper_id);
$q1->execute(); $q1->bind_result($active_count); $q1->fetch(); $q1->close();
$at_capacity = ($active_count >= MAX_CONCURRENT_ORDERS);  // chỉ đánh dấu — KHÔNG info

/** 3) Cooldown (KHÔNG early return) — dùng Accepted_at để chuẩn */
$secs_from_last = null;
$q2 = $conn->prepare("SELECT TIMESTAMPDIFF(SECOND, MAX(Accepted_at), NOW()) FROM orders WHERE ShipperID=? AND Status IN ('accepted','picked_up','in_transit')");
$q2->bind_param('i',$shipper_id);
$q2->execute(); $q2->bind_result($secs_from_last); $q2->fetch(); $q2->close();
$cooldown_remain = 0;
if ($secs_from_last !== null && intval($secs_from_last) < COOLDOWN_SECONDS) {
  $cooldown_remain = COOLDOWN_SECONDS - intval($secs_from_last);
}
// LƯU Ý: KHÔNG set $info cho 2 trường hợp trên, để Android KHÔNG xoá list.
// Việc chặn accept sẽ do accept_order.php xử lý.
if ($at_capacity) {
    // KHÔNG exit, chỉ set info để Android hiển thị cảnh báo
    $info_override = 'max_active_reached';
}

// ... (sau khi tính $cooldown_remain)
if ($cooldown_remain > 0) {
    // KHÔNG exit, chỉ set info
    $info_override = 'cooldown_' . $cooldown_remain;
}

/** 4) Rating boosts / block (có thể bật/tắt toàn bộ) */
$ratingOut = null;
if (USE_RATING_BOOSTS) {
  $rating = null;
  $qr = $conn->prepare("SELECT rating FROM users WHERE ID=? AND Role=6");
  $qr->bind_param('i',$shipper_id);
  $qr->execute(); $qr->bind_result($rating); $qr->fetch(); $qr->close();

  $rating = ($rating !== null) ? floatval($rating) : DEFAULT_NEW_SHIPPER_RATING;
  $ratingOut = $rating;

  if ($rating < HARD_BLOCK_MIN) {
    echo json_encode(['success'=>true,'orders'=>[], 'info'=>'low_rating']);
    $db->dongKetNoi($conn); exit;
  }

  if ($rating >= TIER_EXCELLENT_MIN) {
    $limit  = min($limit + 5, 20);
    $radius = min($radius + 1000, 7000);
  } else if ($rating >= TIER_GOOD_MIN) {
    $limit  = min($limit + 2, 15);
  } else if ($rating >= TIER_WARN_MIN) {
    $limit  = max($limit - 2, 5);
  }
}

/** 5) Lấy vị trí hiện tại & “target” của shipper (đơn active gần nhất) */
$curLat = null; $curLng = null;
$qsPos = $conn->prepare("SELECT lat,lng FROM shipper_locations WHERE shipper_id=?");
$qsPos->bind_param('i',$shipper_id);
$qsPos->execute(); $qsPos->bind_result($curLat,$curLng); $qsPos->fetch(); $qsPos->close();

$tLat = null; $tLng = null;
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

/** 6) Query đơn pending theo khoảng cách (Haversine), sắp xếp tăng dần */
$sql = "
  SELECT 
    o.ID,
    o.CustomerID, o.ShipperID,
    o.Pick_up_address,  o.Pick_up_lat,  o.Pick_up_lng,
    o.Delivery_address, o.Delivery_lat, o.Delivery_lng,
    o.Recipient, o.RecipientPhone, o.Status,
    o.COD_amount, o.Weight, o.ShippingFee, o.Note, o.Created_at,
    (6371000 * 2 * ASIN(SQRT(
        POWER(SIN(RADIANS((? - COALESCE(o.Pick_up_lat,  o.Delivery_lat))/2)),2) +
        COS(RADIANS(COALESCE(o.Pick_up_lat,  o.Delivery_lat))) * COS(RADIANS(?)) *
        POWER(SIN(RADIANS((? - COALESCE(o.Pick_up_lng,  o.Delivery_lng))/2)),2)
    ))) AS distance
  FROM orders o
  WHERE o.Status = 'pending'
    AND (o.Pick_up_lat IS NOT NULL OR o.Delivery_lat IS NOT NULL)
    AND (o.Pick_up_lng IS NOT NULL OR o.Delivery_lng IS NOT NULL)
  HAVING distance <= ?
  ORDER BY distance ASC
  LIMIT ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('dddii', $lat, $lat, $lng, $radius, $limit);
$stmt->execute();
$res = $stmt->get_result();

/** 7) Tính HINT cho từng đơn (để cảnh báo UI) */
$orders = [];
while ($r = $res->fetch_assoc()) {
  $hint_feasible = true;  // mặc định ổn
  $hint_reason   = null;

  $hasPick = isset($r['Pick_up_lat']) && isset($r['Pick_up_lng']);
  $hasDel  = isset($r['Delivery_lat']) && isset($r['Delivery_lng']);
  $pLat    = $hasPick ? floatval($r['Pick_up_lat']) : ($hasDel ? floatval($r['Delivery_lat']) : null);
  $pLng    = $hasPick ? floatval($r['Pick_up_lng']) : ($hasDel ? floatval($r['Delivery_lng']) : null);

  // đủ dữ liệu mới tính hint
  if ($tLat!==null && $tLng!==null && $curLat!==null && $curLng!==null && $pLat!==null && $pLng!==null) {
    // 1) “độ vòng thêm xấp xỉ” (ưu tiên pick-up đồng tuyến)
    $base_km    = haversine_km($curLat,$curLng, floatval($tLat), floatval($tLng));
    $withNew_km = haversine_km($curLat,$curLng, $pLat,$pLng)
                + haversine_km($pLat,$pLng, floatval($tLat), floatval($tLng));
    $added_km   = max(0.0, $withNew_km - $base_km);
    if ($added_km > MAX_ADDED_DETOUR_KM) { $hint_feasible=false; $hint_reason='detour_lon'; }

    // 2) lệch hướng lớn?
    // $bearingToTarget = bearing_deg($curLat,$curLng,floatval($tLat),floatval($tLng));
    // $bearingToNew    = bearing_deg($curLat,$curLng,$pLat,$pLng);
    // $diff            = heading_diff_deg($bearingToTarget,$bearingToNew);
    // if ($diff > MAX_HEADING_DIFF_DEG) { $hint_feasible=false; if ($hint_reason===null) $hint_reason='nguoc_huong'; }

    // 3) target→pickup quá xa?
    $dTarget2New_km = haversine_km(floatval($tLat), floatval($tLng), $pLat, $pLng);
    if ($dTarget2New_km > SECOND_ORDER_MAX_PICKUP_KM) { $hint_feasible=false; if ($hint_reason===null) $hint_reason='xa_pickup'; }
  }

  $orders[] = [
    'ID'              => intval($r['ID']),
    'CustomerID'      => intval($r['CustomerID']),
    'ShipperID'       => $r['ShipperID'] !== null ? intval($r['ShipperID']) : null,
    'Pick_up_address' => $r['Pick_up_address'],
    'Pick_up_lat'     => isset($r['Pick_up_lat']) ? floatval($r['Pick_up_lat']) : null,
    'Pick_up_lng'     => isset($r['Pick_up_lng']) ? floatval($r['Pick_up_lng']) : null,
    'Delivery_address'=> $r['Delivery_address'],
    'Delivery_lat'    => isset($r['Delivery_lat']) ? floatval($r['Delivery_lat']) : null,
    'Delivery_lng'    => isset($r['Delivery_lng']) ? floatval($r['Delivery_lng']) : null,
    'Recipient'       => $r['Recipient'],
    'RecipientPhone'  => $r['RecipientPhone'],
    'Status'          => $r['Status'],
    'COD_amount'      => floatval($r['COD_amount']),
    'Weight'          => floatval($r['Weight']),
    'Shippingfee'     => floatval($r['ShippingFee']),
    'Note'            => $r['Note'],
    'Created_at'      => $r['Created_at'],
    'distance'        => floatval($r['distance']),
    'hint_feasible'   => $hint_feasible,
    'hint_reason'     => $hint_reason
  ];
}
$stmt->close();

/** 8) Trả kết quả
 *  - 'info' chỉ dùng cho OFFLINE/STALE/LOW_RATING để Android ẩn list.
 *  - Trường hợp AT_CAPACITY/COOLDOWN: KHÔNG đặt 'info' (để list vẫn hiển thị).
 */
echo json_encode([
  'success' => true,
  'orders'  => $orders,
  'info'=> isset($info_override) ? $info_override : null, 
  // 'info'    => null,  // đừng trả 'cooldown_...' hoặc 'max_active_reached' ở đây
  'meta'    => [
    'shipper_id'      => $shipper_id,
    'rating'          => $ratingOut,
    'radius_m'        => $radius,
    'limit'           => $limit,
    'at_capacity'     => $at_capacity,
    'cooldown_remain' => $cooldown_remain
  ]
]);

$db->dongKetNoi($conn);
