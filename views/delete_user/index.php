<?php
$id = $_REQUEST['id'];

include_once('controllers/cUser.php');
$p = new controlNguoiDung();
$result = $p->deleteUser($id);

if ($result && isset($result['success']) && $result['success'] === true) {
    echo "<script>alert('Xóa nhân viên thành công!'); window.location.href='?quanlyuser';</script>";
    exit();
} else {
    $error = isset($result['error']) ? $result['error'] : 'Xóa thất bại!';
    echo "<script>alert('Lỗi: $error');</script>";
}
?>
