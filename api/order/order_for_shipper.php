<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include file database.php
include_once("../../config/database.php"); // chỉnh đúng đường dẫn

// Tạo kết nối
$db = new clsKetNoi();
$conn = $db->moKetNoi();
$shipperID = intval($_GET['shipperID']);
// Kiểm tra kết nối
if (!$conn) {
    http_response_code(500);
    echo json_encode(["message" => "Connection failed"]);
    exit();
}

// Query lấy toàn bộ user cùng role
$sql = "SELECT o.ID, o.CustomerID, o.ShipperID, o.Pick_up_address, o.Recipient, o.RecipientPhone,
       o.Delivery_address, o.Status, o.COD_amount, o.Shippingfee, o.Weight, o.Created_at, o.Note, 
       u.Username AS UserName, u.Email AS CustomerEmail,
       u2.Username AS ShipperName, u2.Email AS ShipperEmail, u.PhoneNumber AS PhoneNumberCus
FROM orders o 
LEFT JOIN users u ON o.CustomerID = u.ID 
LEFT JOIN users u2 ON o.ShipperID = u2.ID
WHERE o.ShipperID = $shipperID
ORDER BY Created_at desc";


        // select *, u2.Username AS ShipperName from orders o join users u on o.CustomerID=u.ID LEFT JOIN users u2 ON o.ShipperID = u2.ID

$result = $conn->query($sql);

$users = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Trả JSON
echo json_encode($users);

// Đóng kết nối
$db->dongKetNoi($conn);
?>
