<?php

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    echo "<script>window.location.href='index.php?quanlyuser';</script>";
    exit();
}

include_once('controllers/cUser.php');
$p = new controlNguoiDung();

$id     = intval($_GET['id']);
$status = $_GET['status'];

$result = $p->toggleUserStatus($id, $status);

$redirect_page = 'quanlyuser';

$allowed_pages = ['quanlyuser', 'quanlyshipper', 'listCustomer'];

if (isset($_GET['return']) && in_array($_GET['return'], $allowed_pages)) {
    $redirect_page = $_GET['return'];
}

if (is_array($result) && isset($result['success']) && $result['success']) {
    echo "<script>alert('Cập nhật trạng thái tài khoản thành công');</script>";
} elseif (is_array($result) && isset($result['message'])) {
    echo "<script>alert('{$result['message']}');</script>";
}

echo "<script>window.location.href='index.php?{$redirect_page}';</script>";
exit();
