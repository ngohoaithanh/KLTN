<?php
// FILE: views/lock_user/toggle_user_status.php (PHIÊN BẢN NÂNG CẤP)

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: ?quanlyuser");
    exit();
}

include_once('controllers/cUser.php');
$p = new controlNguoiDung();

$id = intval($_GET['id']);
$status = $_GET['status'];

// Gọi controller để xử lý
$result = $p->toggleUserStatus($id, $status);

// *** PHẦN NÂNG CẤP ***
// Mặc định quay về 'quanlyuser'
$redirect_page = 'quanlyuser'; 

// Kiểm tra xem có trang trả về được chỉ định không
if (isset($_GET['return'])) {
    // Thêm 'listCustomer' vào mảng các trang được phép
    $allowed_pages = ['quanlyuser', 'quanlyshipper', 'listCustomer']; 
    if (in_array($_GET['return'], $allowed_pages)) {
        $redirect_page = $_GET['return'];
    }
}

// Chuyển hướng về trang đã chỉ định
header("Location: ?{$redirect_page}");
exit();
?>