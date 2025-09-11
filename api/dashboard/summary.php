<?php
require_once '../../config/database.php';

$db = new clsKetNoi();
$conn = $db->moKetNoi();

$response = [];

// Tổng số đơn hàng
$sql = "SELECT COUNT(*) AS total_orders FROM orders";
$response['total_orders'] = $conn->query($sql)->fetch_assoc()['total_orders'];

// Tổng thu (COD)
$sql = "SELECT IFNULL(SUM(Amount), 0) AS total_thu FROM cods;";
$response['total_thu'] = $conn->query($sql)->fetch_assoc()['total_thu'];

// Tổng chi (phí vận chuyển)
$sql = "SELECT IFNULL(SUM(ShippingFee), 0) AS total_chi FROM orders";
$response['total_chi'] = $conn->query($sql)->fetch_assoc()['total_chi'];

// Người dùng
$sql = "SELECT COUNT(*) AS total_users FROM users";
$response['total_users'] = $conn->query($sql)->fetch_assoc()['total_users'];

// Thống kê tất cả trạng thái đơn hàng
// Đếm số đơn theo từng trạng thái
$sql = "SELECT Status, COUNT(*) AS count FROM orders GROUP BY Status";
$result = $conn->query($sql);
$statusCounts = [
    'pending' => 0,
    'received' => 0,
    'in_warehouse' => 0,
    'out_of_warehouse' => 0,
    'in_transit' => 0,
    'delivered' => 0,
    'delivery_failed' => 0,
    'returned' => 0,
    'cancelled' => 0
];

while ($row = $result->fetch_assoc()) {
    $status = $row['Status'];
    $count = $row['count'];
    if (isset($statusCounts[$status])) {
        $statusCounts[$status] = (int)$count;
    }
}

$response = array_merge($response, $statusCounts);

// Thống kê người dùng theo vai trò
$sql = "
    SELECT r.Name AS role_name, COUNT(u.ID) AS total_users
    FROM users u
    JOIN roles r ON u.Role = r.ID
    GROUP BY r.Name
";
$result = $conn->query($sql);
$role_counts = [];
while ($row = $result->fetch_assoc()) {
    $role_counts[] = $row;
}
$response['role_counts'] = $role_counts;

// Thống kê COD theo từng trạng thái
$sql = "
    SELECT c.Status, SUM(c.Amount) AS total_cod
    FROM cods c
    GROUP BY c.Status
";
$result = $conn->query($sql);
$cod_by_status = [];
while ($row = $result->fetch_assoc()) {
    $cod_by_status[] = $row;
}
$response['cod_by_status'] = $cod_by_status;


$db->dongKetNoi($conn);

header('Content-Type: application/json');
echo json_encode($response);

?>