<?php
// FILE: api/notification/get_admin_notifs.php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");

// Giả sử Admin luôn có ID là 1 (Hoặc bạn lấy từ session nếu post từ JS)
// Trong thực tế, bạn nên gửi UserID của người đang đăng nhập lên
$admin_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 1;

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Lấy 5 thông báo mới nhất chưa đọc (hoặc tất cả)
$sql = "SELECT * FROM notifications 
        WHERE UserID = ? 
        ORDER BY Created_at DESC 
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$notifs = [];
$unread_count = 0;

// Đếm tổng số chưa đọc (để hiện số đỏ trên cái chuông)
$count_sql = "SELECT COUNT(*) as total FROM notifications WHERE UserID = ? AND IsRead = 0";
$stmt_count = $conn->prepare($count_sql);
$stmt_count->bind_param("i", $admin_id);
$stmt_count->execute();
$unread_count = $stmt_count->get_result()->fetch_assoc()['total'];

while ($row = $result->fetch_assoc()) {
    // Định dạng thời gian cho đẹp (VD: 2 phút trước)
    $row['TimeAgo'] = time_elapsed_string($row['Created_at']);
    
    // Icon tùy theo loại thông báo
    if ($row['Type'] == 'order') $row['Icon'] = 'fa-file-alt';
    else if ($row['Type'] == 'system') $row['Icon'] = 'fa-exclamation-triangle';
    else $row['Icon'] = 'fa-info-circle';
    
    // Màu icon
    if ($row['Type'] == 'order') $row['Color'] = 'bg-primary';
    else if ($row['Type'] == 'system') $row['Color'] = 'bg-warning';
    else $row['Color'] = 'bg-info';

    $notifs[] = $row;
}

echo json_encode([
    'count' => $unread_count,
    'data' => $notifs
]);

$db->dongKetNoi($conn);

// Hàm phụ trợ: Tính thời gian trôi qua
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'năm',
        'm' => 'tháng',
        'w' => 'tuần',
        'd' => 'ngày',
        'h' => 'giờ',
        'i' => 'phút',
        's' => 'giây',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' trước' : 'vừa xong';
}
?>