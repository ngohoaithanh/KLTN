<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Sửa lại đường dẫn nếu cần

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

if (isset($_GET['shipper_id']) && is_numeric($_GET['shipper_id'])) {
    $shipperId = intval($_GET['shipper_id']);

    // Lấy xe đang hoạt động (is_active = TRUE) của shipper
    $stmt = $conn->prepare("SELECT license_plate, model FROM vehicles WHERE shipper_id = ? AND is_active = TRUE LIMIT 1");
    $stmt->bind_param("i", $shipperId);

    if ($stmt->execute()) {
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            // Trả về đối tượng vehicle
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Không tìm thấy thông tin xe.']);
        }
    } else {
        echo json_encode(['error' => 'Lỗi truy vấn.']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Thiếu shipper_id.']);
}

$db->dongKetNoi($conn);
?>