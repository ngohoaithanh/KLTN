<?php
$id = $_REQUEST['id'];

include_once('controllers/cOrder.php'); // Đường dẫn controller xử lý đơn hàng
$p = new controlOrder();                // Khởi tạo controller đơn hàng
$result = $p->deleteOrder($id);         // Gọi hàm xóa

if ($result && isset($result['success']) && $result['success'] === true) {
    echo "<script>alert('Xóa đơn hàng thành công!'); window.location.href='?quanlydonhang';</script>";
    exit();
} else {
    $error = isset($result['error']) ? $result['error'] : 'Xóa đơn hàng thất bại!';
    echo "<script>alert('Lỗi: $error');</script>";
}
?>
