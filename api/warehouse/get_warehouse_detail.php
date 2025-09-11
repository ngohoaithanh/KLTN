<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once("../../config/database.php");

$db = new clsKetNoi();
$conn = $db->moKetNoi();

$warehouse_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($warehouse_id <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "ID kho không hợp lệ."]);
    exit();
}

$sql = "
    SELECT 
        owt.*, 
        w.name AS warehouse_name, 
        w.operation_status,
        w.Address,
        u.Username AS user_name
    FROM 
        order_warehouse_tracking owt
    JOIN 
        warehouses w ON owt.WarehouseID = w.ID
    LEFT JOIN 
        users u ON owt.Handled_by = u.ID
    WHERE 
        owt.WarehouseID = $warehouse_id
    ORDER BY 
        owt.Timestamp DESC
";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode(["message" => "Không có dữ liệu tracking cho kho này."]);
}

$db->dongKetNoi($conn);
?>
