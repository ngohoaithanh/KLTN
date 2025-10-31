<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Sửa lại đường dẫn nếu cần

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = ['exists' => false];

if (isset($_GET['phone_number'])) {
    $phone = $_GET['phone_number'];

    // Đảm bảo SĐT có định dạng +84 giống như Firebase trả về
    // Hoặc bạn có thể chuẩn hóa SĐT (ví dụ: bỏ +84 và thêm 0)

    $stmt = $conn->prepare("SELECT ID FROM users WHERE PhoneNumber = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['exists'] = true;
    }
    $stmt->close();
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>