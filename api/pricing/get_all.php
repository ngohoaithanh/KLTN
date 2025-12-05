<?php
// FILE: api/pricing/get_all.php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");
$db = new clsKetNoi(); $conn = $db->moKetNoi();

$sql = "SELECT * FROM pricing_rules WHERE IsDeleted = 0 ORDER BY ID DESC";
$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) { $data[] = $row; }

echo json_encode($data);
$db->dongKetNoi($conn);
?>