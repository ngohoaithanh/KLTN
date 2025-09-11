<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

$response = [];

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Chỉ chấp nhận phương thức POST']);
    exit;
}

// Lấy dữ liệu từ input
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (
    isset($input['name'], $input['address'], $input['capacity'], 
          $input['operation_status'], $input['manager_id']) &&
    !empty($input['name']) && 
    !empty($input['address']) &&
    is_numeric($input['capacity']) &&
    in_array($input['operation_status'], ['active', 'full', 'paused']) &&
    is_numeric($input['manager_id'])
) {
    $name = trim($input['name']);
    $address = trim($input['address']);
    $capacity = intval($input['capacity']);
    $operation_status = $input['operation_status'];
    $manager_id = intval($input['manager_id']);

    // Kiểm tra manager
    $stmt = $conn->prepare("SELECT ID FROM users WHERE ID = ?");
    $stmt->bind_param("i", $manager_id);
    $stmt->execute();
    $checkUser = $stmt->get_result();

    if ($checkUser->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'Manager không tồn tại!']);
    } else {
        $sql = "INSERT INTO warehouses (Name, Address, Quantity, Capacity, manager_id, operation_status, created_at)
                VALUES (?, ?, 0, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiis", $name, $address, $capacity, $manager_id, $operation_status);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Thêm kho hàng thành công!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Lỗi khi thêm kho hàng: ' . $conn->error]);
        }
        $stmt->close();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu dữ liệu hoặc dữ liệu không hợp lệ!']);
}

$db->dongKetNoi($conn);
?>