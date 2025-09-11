<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $id = intval($_REQUEST['id']);

    $sql = "UPDATE orders SET Status = 'in_warehouse' WHERE ID = $id";
    $result = $conn->query($sql);

    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID đơn hàng']);
}

$db->dongKetNoi($conn);
?>
