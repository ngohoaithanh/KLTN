<?php
include_once('../../config/database.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];

    // Truy vấn xóa đơn hàng theo ID
    // $sql = "DELETE FROM orders WHERE ID = '$id'";
    $sql = "UPDATE orders set hidden = 0 WHERE ID = '$id'";
    $result = $conn->query($sql);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }

    $db->dongKetNoi($conn);
} else {
    echo json_encode(['success' => false, 'error' => 'ID không hợp lệ']);
}
?>
