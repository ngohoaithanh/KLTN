<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include file database.php
include_once("../../config/database.php"); // chỉnh đúng đường dẫn

// Tạo kết nối
$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Kiểm tra kết nối
if (!$conn) {
    http_response_code(500);
    echo json_encode(["message" => "Connection failed"]);
    exit();
}

// Query lấy toàn bộ user cùng role
$sql = "SELECT 
            u.ID,
            u.Username,
            u.Email,
            u.PhoneNumber,
            u.Note,
            r.Name AS RoleName,
            r.Description AS RoleDescription,
            w.Name AS warehouse_name
        FROM users u
        LEFT JOIN roles r ON u.Role = r.ID 
        LEFT JOIN warehouses w ON w.ID = u.warehouse_id
        WHERE u.Role !=7 AND hidden = 1
        ORDER BY u.ID DESC";

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
