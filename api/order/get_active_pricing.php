<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

if ($conn) {
    // Lấy bảng giá đang kích hoạt (IsActive = 1)
    // Ưu tiên lấy loại xe 'motorbike' (hoặc bạn có thể truyền tham số type lên)
    $sql = "SELECT * FROM pricing_rules WHERE IsActive = 1 AND VehicleType = 'motorbike' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rule = $result->fetch_assoc();
        $response = [
            'success' => true,
            'data' => [
                'BaseDistance' => floatval($rule['BaseDistance']),
                'BasePrice'    => floatval($rule['BasePrice']),
                'PricePerKm'   => floatval($rule['PricePerKm']),
                'PricePerKg'   => floatval($rule['PricePerKg']),
                'FreeWeight'   => floatval($rule['FreeWeight']) // <-- THÊM DÒNG NÀY
            ]
        ];
    } else {
        // Cập nhật cả phần dữ liệu mặc định (fallback)
        $response = [
            'success' => true,
            'data' => [
                'BaseDistance' => 2.0,
                'BasePrice'    => 15000,
                'PricePerKm'   => 5000,
                'PricePerKg'   => 2500,
                'FreeWeight'   => 3.0 // <-- THÊM MẶC ĐỊNH LÀ 3KG
            ],
            'message' => 'Using default pricing'
        ];
    }
} else {
    $response = ['success' => false, 'error' => 'Lỗi kết nối'];
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>