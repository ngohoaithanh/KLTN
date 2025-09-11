<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once("../../config/database.php");

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// if (!$conn) {
//     http_response_code(500);
//     echo json_encode(["message" => "Lỗi kết nối cơ sở dữ liệu"]);
//     exit();
// }

$operation_status = isset($_GET['operation_status']) ? $conn->real_escape_string($_GET['operation_status']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$conditions = [];

if (!empty($operation_status)) {
    $conditions[] = "w.operation_status = '$operation_status'";
}

if (!empty($search)) {
    $conditions[] = "(w.Name LIKE '%$search%' OR w.Address LIKE '%$search%')";
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$sql = "SELECT w.*, u.Username AS manager_username
        FROM warehouses w 
        LEFT JOIN users u ON w.manager_id = u.ID 
        $where 
        ORDER BY w.ID asc";
// $sql = "SELECT w.*, u.ID AS manager_user_id, u.Username AS manager_username 
//         FROM warehouses w 
//         JOIN users u ON w.manager_id = u.ID 
//         $where 
//         ORDER BY w.ID asc";//$where ORDER BY id DESC cuoi dong
$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);

$db->dongKetNoi($conn);
?>
