<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

include_once("../../config/database.php");

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (!$conn) {
    http_response_code(500);
    echo json_encode(["message" => "Connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['id'])) {
    http_response_code(400);
    echo json_encode(["message" => "Missing ID"]);
    exit();
}

$id = (int)$data['id'];

$sql = "DELETE FROM warehouses WHERE id = $id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Warehouse deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to delete warehouse"]);
}

$db->dongKetNoi($conn);
?>
