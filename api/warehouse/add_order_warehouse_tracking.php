<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (
    isset($_REQUEST['order_id'], $_REQUEST['warehouse_id'], $_REQUEST['action']) &&
    !empty($_REQUEST['order_id']) && !empty($_REQUEST['warehouse_id']) && !empty($_REQUEST['action'])
) {
    $order_id     = intval($_REQUEST['order_id']);
    $warehouse_id = intval($_REQUEST['warehouse_id']);
    $action       = trim($_REQUEST['action']);
    $note         = trim($_REQUEST['note'] ?? '');
    
    $sql = "INSERT INTO order_warehouse_tracking (OrderID, WarehouseID, Action, Note, Created_at)
            VALUES ($order_id, $warehouse_id, '$action', '$note', NOW())";
    
    $result = $conn->query($sql);

    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu dữ liệu bắt buộc']);
}

$db->dongKetNoi($conn);
?>
