<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT o.ID, o.CustomerID, o.ShipperID, o.Pick_up_address, o.Pick_up_lat, o.Pick_up_lng, o.Recipient, o.RecipientPhone,
       o.Delivery_address, o.Delivery_lat, o.Delivery_lng, o.Status, o.COD_amount, o.Shippingfee, o.Weight, o.Created_at, o.Note, o.CODFee,  
       u.Username AS UserName, u.Email AS CustomerEmail,
       u2.Username AS ShipperName, u2.Email AS ShipperEmail, u.PhoneNumber AS PhoneNumberCus
    FROM orders o 
    LEFT JOIN users u ON o.CustomerID = u.ID 
    LEFT JOIN users u2 ON o.ShipperID = u2.ID
    WHERE o.ID = $id";
    $result = $conn->query($sql);
    // $query = "SELECT * FROM orders WHERE ID = $id";
    // $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo json_encode($order);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy đơn hàng']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID hợp lệ']);
}

$db->dongKetNoi($conn);
?>
