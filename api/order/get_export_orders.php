<?php
header('Content-Type: application/json');
include_once('../../config/database.php');

$warehouseID = $_GET['warehouseID'] ?? null;
$db = new clsKetNoi();
$conn = $db->moKetNoi();
if (!$warehouseID) {
    echo json_encode(['success' => false, 'message' => 'Thiếu warehouseID']);
    exit;
}

$sql = "SELECT o.*, u.Username AS CustomerName, s.Username AS ShipperName 
        FROM orders o 
        JOIN users u ON o.CustomerID = u.ID 
        LEFT JOIN users s ON o.ShipperID = s.ID
        WHERE o.WarehouseID = $warehouseID  
        AND o.Status NOT IN ('pending', 'received', 'in_warehouse')";
$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode(["message" => "Không có đơn hàng nào."]);
}

$db->dongKetNoi($conn);
?>

