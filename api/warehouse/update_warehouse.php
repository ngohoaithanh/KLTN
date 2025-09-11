<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, POST");

include_once("../../config/database.php");

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (!$conn) {
    http_response_code(500);
    echo json_encode(["message" => "Connection failed"]);
    exit();
}

// Lấy dữ liệu JSON từ body hoặc từ POST thông thường
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST; // fallback khi frontend dùng form POST thông thường
}

// Kiểm tra dữ liệu đầu vào
if (
    !isset($data['id']) || !isset($data['name']) || !isset($data['address']) ||
    !isset($data['capacity']) || !isset($data['manager_id']) || !isset($data['operation_status'])
) {
    http_response_code(400);
    echo json_encode(["message" => "Missing required fields"]);
    exit();
}

// Lấy dữ liệu và escape
$id = (int)$data['id'];
$name = $conn->real_escape_string(trim($data['name']));
$address = $conn->real_escape_string(trim($data['address']));
$capacity = (int)$data['capacity'];
$manager_id = (int)$data['manager_id'];
$operation_status = $conn->real_escape_string($data['operation_status']);

$sql = "UPDATE warehouses SET 
            name = '$name',
            address = '$address',
            capacity = $capacity,
            manager_id = $manager_id,
            operation_status = '$operation_status',
            updated_at = NOW()
        WHERE id = $id";

if ($conn->query($sql)) {
    // echo json_encode(["message" => "Warehouse updated successfully"]);
    echo json_encode([
    "success" => true,
    "message" => "Warehouse updated successfully"
]);
} else {
    http_response_code(500);
    echo json_encode([
        "message" => "Failed to update warehouse",
        "error" => $conn->error // Gợi ý lỗi để debug (có thể xóa đi trong production)
    ]);
}

$db->dongKetNoi($conn);
?>
