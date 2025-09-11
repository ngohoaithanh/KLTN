<?php
session_start();
include_once('../../config/database.php');
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['CustomerID']) || empty($_SESSION['CustomerID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn phải đăng nhập']);
    exit();
}

$customerID = $_SESSION['CustomerID'];
$role = $_SESSION['role'];

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($role == 7) {
    // Nếu là khách hàng → chỉ lấy đơn hàng của khách này
    $query = "SELECT o.*, u.Username, s.Name AS ShipperName 
              FROM orders o
              LEFT JOIN users u ON o.CustomerID = u.ID
              LEFT JOIN shippers s ON o.ShipperID = s.ID
              WHERE o.CustomerID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customerID);
} else {
    // Nếu là admin hoặc nhân viên → lấy tất cả đơn hàng
    $query = "SELECT o.*, u.Username, s.Name AS ShipperName 
              FROM orders o
              LEFT JOIN users u ON o.CustomerID = u.ID
              LEFT JOIN shippers s ON o.ShipperID = s.ID";
    $stmt = $conn->prepare($query);
}

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if (!empty($data)) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng']);
}

$stmt->close();
$db->dongKetNoi($conn);
?>
