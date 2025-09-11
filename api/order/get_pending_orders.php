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

$sql = "SELECT o.*, u.Username AS CustomerName FROM orders o JOIN users u on o.CustomerID=u.ID WHERE WarehouseID = $warehouseID AND Status = 'pending'";
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

