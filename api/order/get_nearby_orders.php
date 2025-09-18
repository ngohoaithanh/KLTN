<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// ===== Lấy input =====
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : 0;
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : 0;
$radius = isset($_GET['radius']) ? floatval($_GET['radius']) : 5000; // mét
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

if ($lat == 0 || $lng == 0) {
    echo json_encode(['success' => false, 'error' => 'Thiếu lat/lng']);
    exit();
}

// ===== Query tìm đơn gần =====
// Giả sử orders có cột: Pick_up_lat, Pick_up_lng
// Dùng công thức Haversine để tính khoảng cách (mét)
$sql = "
    SELECT 
        o.ID, o.CustomerID, o.Pick_up_address, o.Delivery_address,
        o.Recipient, o.RecipientPhone, o.Status, 
        o.COD_amount, o.ShippingFee, o.Weight,
        o.Pick_up_lat, o.Pick_up_lng, o.Delivery_lat, o.Delivery_lng,
        o.Created_at, o.Note,
        (6371000 * ACOS(
            COS(RADIANS(?)) * COS(RADIANS(o.Pick_up_lat)) *
            COS(RADIANS(o.Pick_up_lng) - RADIANS(?)) +
            SIN(RADIANS(?)) * SIN(RADIANS(o.Pick_up_lat))
        )) AS distance
    FROM orders o
    WHERE o.hidden = 1
      AND o.Status IN ('pending', 'out_of_warehouse') 
      AND o.Pick_up_lat IS NOT NULL 
      AND o.Pick_up_lng IS NOT NULL
    HAVING distance <= ?
    ORDER BY distance ASC
    LIMIT ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("dddii", $lat, $lng, $lat, $radius, $limit);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode([
    'success' => true,
    'count' => count($orders),
    'orders' => $orders
]);

$stmt->close();
$db->dongKetNoi($conn);
