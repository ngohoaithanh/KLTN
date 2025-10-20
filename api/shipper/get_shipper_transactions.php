<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

if (isset($_GET['shipper_id']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $shipperId = intval($_GET['shipper_id']);
    // Thêm giờ phút giây để bao trọn cả ngày
    $startDate = $_GET['start_date'] . ' 00:00:00';
    $endDate = $_GET['end_date'] . ' 23:59:59';

    if ($conn) {
        // Sử dụng Prepared Statements
        $stmt = $conn->prepare("
            SELECT ID, OrderID, Type, Amount, Status, Note, Created_at
            FROM transactions
            WHERE UserID = ? AND Created_at BETWEEN ? AND ?
            ORDER BY Created_at DESC
        ");
        $stmt->bind_param("iss", $shipperId, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        $stmt->close();

    } else {
        http_response_code(500);
        $response = ["error" => "Connection failed"];
    }
} else {
    http_response_code(400);
    $response = ["error" => "Thiếu thông tin shipper_id hoặc khoảng thời gian."];
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>