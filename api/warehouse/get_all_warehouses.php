<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once("../../config/database.php");

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (!$conn) {
    http_response_code(500);
    echo json_encode(["message" => "Connection failed"]);
    exit();
}

// Lọc theo stock_status và operation_status nếu có
$conditions = [];
if (!empty($_GET['stock_status'])) {
    $stock_status = $conn->real_escape_string($_GET['stock_status']);
    $conditions[] = "stock_status = '$stock_status'";
}
if (!empty($_GET['operation_status'])) {
    $operation_status = $conn->real_escape_string($_GET['operation_status']);
    $conditions[] = "operation_status = '$operation_status'";
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$sql = "SELECT w.*, u.ID AS manager_user_id, u.Username AS manager_username 
        FROM warehouses w 
        JOIN users u ON w.manager_id = u.ID 
        $where 
        ORDER BY w.ID asc";//$where ORDER BY id DESC cuoi dong
$result = $conn->query($sql);

$warehouses = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $warehouses[] = $row;
    }
}

echo json_encode($warehouses);

$db->dongKetNoi($conn);
?>
