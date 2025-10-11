<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include file database.php
include_once("../../config/database.php"); // Đảm bảo đường dẫn này chính xác

// Tạo kết nối
$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Khởi tạo mảng kết quả
$response_data = [];

// Kiểm tra xem CustomerID có được cung cấp và là số hợp lệ không
if (isset($_GET['CustomerID']) && is_numeric($_GET['CustomerID'])) {
    $customerID = intval($_GET['CustomerID']);

    // Kiểm tra kết nối
    if ($conn) {
        // BƯỚC 1: Viết câu lệnh SQL với dấu "?" để chống SQL Injection
        $sql = "SELECT 
                    o.ID, o.CustomerID, o.ShipperID, o.Pick_up_address, o.Recipient, o.RecipientPhone,
                    o.Delivery_address, o.Status, o.COD_amount, o.Shippingfee, o.Weight, o.Created_at, o.Note, 
                    u.Username AS UserName, u.Email AS CustomerEmail,
                    u2.Username AS ShipperName, u2.Email AS ShipperEmail, u.PhoneNumber AS PhoneNumberCus
                FROM orders o 
                LEFT JOIN users u ON o.CustomerID = u.ID 
                LEFT JOIN users u2 ON o.ShipperID = u2.ID
                WHERE o.CustomerID = ?
                ORDER BY Created_at DESC";

        // BƯỚC 2: Chuẩn bị câu lệnh
        $stmt = $conn->prepare($sql);

        // BƯỚC 3: Gắn (bind) biến vào câu lệnh. "i" có nghĩa là kiểu integer.
        $stmt->bind_param("i", $customerID);

        // BƯỚC 4: Thực thi
        $stmt->execute();

        // BƯỚC 5: Lấy kết quả
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response_data[] = $row;
            }
        }
        
        // Đóng statement
        $stmt->close();

    } else {
        http_response_code(500);
        $response_data = ["message" => "Connection failed"];
    }

} else {
    http_response_code(400); // Bad Request
    $response_data = ["message" => "Thiếu hoặc sai CustomerID"];
}

// Đóng kết nối
$db->dongKetNoi($conn);

// Trả về kết quả dưới dạng JSON
echo json_encode($response_data);
?>