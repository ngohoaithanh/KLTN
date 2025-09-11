<?php
include_once('../../config/database.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    // $sql = "DELETE FROM users WHERE ID = '$id'";
    $sql = "UPDATE users set hidden = 0 WHERE ID = '$id'";
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
