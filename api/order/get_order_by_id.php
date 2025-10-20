<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $orderId = intval($_GET['id']);

    // BƯỚC 1: Lấy thông tin chính của đơn hàng và thông tin xe
    // Sử dụng Prepared Statements để chống lỗi bảo mật SQL Injection
    $stmt = $conn->prepare("
        SELECT 
            o.ID, o.CustomerID, o.ShipperID, o.Pick_up_address, o.Pick_up_lat, o.Pick_up_lng, 
            o.Recipient, o.RecipientPhone, o.Delivery_address, o.Delivery_lat, o.Delivery_lng, 
            o.Status, o.COD_amount, o.Shippingfee, o.Weight, o.Created_at, o.Note, o.CODFee, o.is_rated, o.fee_payer, 
            u.Username AS UserName, u.Email AS CustomerEmail, u.PhoneNumber AS PhoneNumberCus,
            u2.Username AS ShipperName, u2.Email AS ShipperEmail,u2.PhoneNumber AS ShipperPhoneNumber,
            u2.rating AS ShipperRating,
            v.license_plate, v.model AS vehicle_model
        FROM orders o 
        LEFT JOIN users u ON o.CustomerID = u.ID 
        LEFT JOIN users u2 ON o.ShipperID = u2.ID
        LEFT JOIN vehicles v ON o.ShipperID = v.shipper_id AND v.is_active = TRUE
        WHERE o.ID = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Lấy dữ liệu đơn hàng
        $orderData = $result->fetch_assoc();
        $response = $orderData;

        // Tạo một đối tượng lồng nhau cho thông tin xe để JSON sạch sẽ hơn
        $response['vehicle'] = [
            'license_plate' => $orderData['license_plate'],
            'model' => $orderData['vehicle_model']
        ];
        // Xóa các key không cần thiết ở cấp cao nhất
        unset($response['license_plate'], $response['vehicle_model']);

        // BƯỚC 2: Lấy toàn bộ lịch sử tracking của đơn hàng
        $stmt_track = $conn->prepare("SELECT Status, Updated_at FROM trackings WHERE OrderID = ? ORDER BY Updated_at DESC");
        $stmt_track->bind_param("i", $orderId);
        $stmt_track->execute();
        $track_result = $stmt_track->get_result();
        
        $tracking_history = [];
        while ($row = $track_result->fetch_assoc()) {
            $tracking_history[] = $row;
        }
        
        // Thêm lịch sử tracking vào response
        $response['tracking_history'] = $tracking_history;

    } else {
        $response = ['success' => false, 'error' => 'Không tìm thấy đơn hàng'];
    }
    $stmt->close();

} else {
    $response = ['success' => false, 'error' => 'Thiếu ID hợp lệ'];
}

$db->dongKetNoi($conn);

// BƯỚC 3: Gộp tất cả dữ liệu và trả về JSON cuối cùng
echo json_encode($response);
?>