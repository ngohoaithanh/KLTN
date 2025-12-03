<?php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");
$db = new clsKetNoi(); $conn = $db->moKetNoi();

$sql = "SELECT * FROM pricing_rules ORDER BY ID DESC";
$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) { $data[] = $row; }

echo json_encode($data);
$db->dongKetNoi($conn);
?>