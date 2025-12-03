<?php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");
$db = new clsKetNoi(); $conn = $db->moKetNoi();
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $stmt = $conn->prepare("DELETE FROM pricing_rules WHERE ID = ?");
    $stmt->bind_param("i", $data['id']);
    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'error' => $conn->error]);
    $stmt->close();
}
$db->dongKetNoi($conn);
?>