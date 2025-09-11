<?php

// Kết nối database
include_once("../../config/database.php");

header('Content-Type: application/json; charset=utf-8');

// Nhận từ khóa tìm kiếm từ URL
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
} else {
    echo json_encode(["error" => "Missing keyword parameter"]);
    exit;
}

$p = new clsKetNoi();
$conn = $p->moKetNoi();

// Chống SQL Injection
$keyword = $conn->real_escape_string($keyword);

// Tìm kiếm đơn hàng theo ID
$sql = "SELECT o.ID, o.CustomerID, o.ShipperID, o.Pick_up_address, o.Delivery_address, 
       o.Status, o.COD_amount, o.Created_at, o.Note, 
       u.Username , u.Email AS CustomerEmail,
       u2.Username AS ShipperName, u2.Email AS ShipperEmail
FROM orders o 
JOIN users u ON o.CustomerID = u.ID 
LEFT JOIN users u2 ON o.ShipperID = u2.ID
WHERE o.ID = '$keyword'";

 // Lọc theo ID

$result = $conn->query($sql);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    error_log(print_r($row, true));  // Ghi ra log để kiểm tra dữ li
    $orders[] = [
        'ID' => $row['ID'],
        'Username' => isset($row['Username']) ? $row['Username'] : 'Không có tên khách hàng',
        'ShipperName' => isset($row['ShipperName']) ? $row['ShipperName'] : 'Không có thông tin shipper',
        'Delivery_address' => isset($row['Delivery_address']) ? $row['Delivery_address'] : 'Không có địa chỉ',
        'Created_at' => isset($row['Created_at']) ? $row['Created_at'] : 'Không có thời gian',
        'COD_amount' => isset($row['COD_amount']) ? $row['COD_amount'] : 0,
        'Status' => isset($row['Status']) ? $row['Status'] : 'Chưa có trạng thái',
        'Note' => isset($row['Note']) ? $row['Note'] : 'Không có ghi chú'
    ];
}


// Đóng kết nối
$p->dongKetNoi($conn);

// Trả về JSON
echo json_encode($orders);
?>
