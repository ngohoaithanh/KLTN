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

// Câu truy vấn: tìm kiếm theo Username hoặc Name (role sẽ join để lấy luôn)
$sql = "SELECT u.ID, u.Username, u.PhoneNumber, u.Email, r.Name AS RoleName, w.Name AS warehouse_name
        FROM users u 
        JOIN roles r ON u.Role = r.ID
        LEFT JOIN warehouses w ON w.ID = u.warehouse_id
        WHERE u.Username LIKE '%$keyword%' OR r.Name LIKE '%$keyword%'";

$result = $conn->query($sql);

$users = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'ID' => $row['ID'],
            'Username' => $row['Username'],
            'PhoneNumber' => $row['PhoneNumber'],
            'Email' => $row['Email'],
            'RoleName' => $row['RoleName'],
            'warehouse_name' => $row['warehouse_name']
        ];
    }
}

// Đóng kết nối
$p->dongKetNoi($conn);

// Trả về JSON
echo json_encode($users);
?>
