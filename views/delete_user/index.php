<?php
$id = $_REQUEST['id'];

include_once('controllers/cUser.php');
include_once('controllers/cLog.php');
$current_user_id = $_SESSION['user_id'] ?? 0;
$p = new controlNguoiDung();
$result = $p->deleteUser($id);

if ($result && isset($result['success']) && $result['success'] === true) {
    if ($current_user_id > 0) {
        $log_desc = "Xóa người dùng (UserID = $id) bởi AdminID = $current_user_id";

        controlLog::record(
            $current_user_id,               // Ai xóa
            'DELETE_USER',                  // Hành động
            'users',                        // Bảng
            $id,                            // ID bị xóa
            $log_desc                       // Mô tả chi tiết
        );
    }
    echo "<script>alert('Xóa nhân viên thành công!'); window.location.href='?quanlyuser';</script>";
    exit();
} else {
    $error = isset($result['error']) ? $result['error'] : 'Xóa thất bại!';
    echo "<script>alert('Lỗi: $error');</script>";
}
?>
