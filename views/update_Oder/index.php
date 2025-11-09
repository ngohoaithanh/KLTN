<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('controllers/cOrder.php');
include_once('config/database.php');

$p = new controlOrder();
$db = new clsKetNoi();
$conn = $db->moKetNoi();


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $order = $p->getOrderById($id);
}

if (!$order) {
    echo "<div class='alert alert-danger text-center mt-4'>Không tìm thấy đơn hàng với ID = $id</div>";
    echo "<div class='text-center'><a href='?quanlydonhang' class='btn btn-primary mt-2'>Quay lại quản lý đơn hàng</a></div>";
    return;
}


// Cập nhật đơn hàng
if (isset($_POST['submit'])) {
    // Lấy trọng lượng từ form
    $weight = floatval($_POST['Weight']);

    // Tính phí ship theo công thức bạn đã cung cấp
    if ($weight <= 1) {
        $shippingFee = 15000;
    } elseif ($weight <= 2) {
        $shippingFee = 18000;
    } else {
        $extraWeight = $weight - 2;
        $extraFee = ceil($extraWeight * 2) * 2500;
        $shippingFee = 18000 + $extraFee;
    }

    // Dữ liệu cập nhật đơn hàng
    $data = [
        "id" => $_POST['id'],
        "CustomerID" => $_POST['CustomerID'],
        "FullName" => $_POST['FullName'],
        "PhoneNumber" => $_POST['PhoneNumber'],
        "ShipperID" => 1,
        "Pick_up_address" => $_POST['Pick_up_address'],
        "Delivery_address" => $_POST['Delivery_address'],
        "Recipient" => $_POST['Recipient'],
        "RecipientPhone" => $_POST['RecipientPhone'],
        "Weight" => $weight,
        "ShippingFee" => $shippingFee, // Thêm phí ship vào dữ liệu
        "Status" => $_POST['Status'],
        "COD_amount" => $_POST['COD_amount'],
        "Note" => $_POST['Note']
    ];

    // Cập nhật đơn hàng
    $result = $p->updateOrder($data);
    if ($result && isset($result['success']) && $result['success'] === true) {
        echo "<script>alert('Cập nhật đơn hàng thành công!'); window.location.href='?quanlydonhang';</script>";
    } else {
        $msg = isset($result['message']) ? $result['message'] : 'Cập nhật thất bại!';
        echo "<script>alert('$msg');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Đơn Hàng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="add-staff-form" style="margin-top: 50px;">
    <h2 class="add-staff-title">Cập Nhật Đơn Hàng</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($order['ID']) ?>">
        <input type="hidden" name="CustomerID" value="<?= htmlspecialchars($order['CustomerID']) ?>">

        <div class="form-group-add">
            <label>Tên Khách Hàng</label>
            <input type="text" name="FullName" class="form-control-add form-control" value="<?= htmlspecialchars($order['FullName'] ?? '') ?>" required>
        </div>

        <div class="form-group-add">
            <label>SĐT Khách Hàng</label>
            <input type="text" name="PhoneNumber" class="form-control-add form-control" value="<?= htmlspecialchars($order['PhoneNumber'] ?? '') ?>" required pattern="[0-9]{10,11}">
        </div>

        <div class="form-group-add">
            <label>Địa Chỉ Lấy Hàng</label>
            <input type="text" name="Pick_up_address" class="form-control-add form-control" value="<?= htmlspecialchars($order['Pick_up_address']) ?>" required>
        </div>

        <div class="form-group-add">
            <label>Địa Chỉ Giao Hàng</label>
            <input type="text" name="Delivery_address" class="form-control-add form-control" value="<?= htmlspecialchars($order['Delivery_address']) ?>" required>
        </div>

        <div class="form-group-add">
            <label>Người Nhận Hàng</label>
            <input type="text" name="Recipient" class="form-control-add form-control" value="<?= htmlspecialchars($order['Recipient']) ?>" required>
        </div>

        <div class="form-group-add">
            <label>SĐT Người Nhận</label>
            <input type="text" name="RecipientPhone" class="form-control-add form-control" value="<?= htmlspecialchars($order['RecipientPhone'] ?? '') ?>" required pattern="[0-9]{10,11}">
        </div>

        <div class="form-group-add">
            <label>Trọng Lượng (kg)</label>
            <input type="number" name="Weight" step="0.01" class="form-control-add form-control" value="<?= htmlspecialchars($order['Weight'] ?? '') ?>" required>
        </div>
        <div class="form-group-add">
            <label>Trạng Thái Đơn Hàng</label>
            <select name="Status" class="form-control-add form-control" required>
                <option value="">-- Chọn trạng thái --</option>
                <?php $currentStatus = $order['Status'] ?? ''; // Lấy trạng thái hiện tại hoặc chuỗi rỗng ?>
                <option value="pending" <?= ($currentStatus == 'pending' ? 'selected' : '') ?>>Đang chờ xử lý (Pending)</option>
                <option value="accepted" <?= ($currentStatus == 'accepted' ? 'selected' : '') ?>>Đã xác nhận (Accepted)</option>
                <option value="picked_up" <?= ($currentStatus == 'picked_up' ? 'selected' : '') ?>>Đã lấy hàng (Picked Up)</option>
                <option value="in_transit" <?= ($currentStatus == 'in_transit' ? 'selected' : '') ?>>Đang vận chuyển (In Transit)</option>
                <option value="delivered" <?= ($currentStatus == 'delivered' ? 'selected' : '') ?>>Đã giao hàng (Delivered)</option>
                <option value="delivery_failed" <?= ($currentStatus == 'delivery_failed' ? 'selected' : '') ?>>Giao hàng thất bại (Delivery Failed)</option>
                <option value="cancelled" <?= ($currentStatus == 'cancelled' ? 'selected' : '') ?>>Đã hủy (Cancelled)</option>
            </select>
        </div>


        <div class="form-group-add">
            <label>Tiền COD</label>
            <input type="number" name="COD_amount" class="form-control-add form-control" value="<?= htmlspecialchars($order['COD_amount']) ?>" step="1000" required>
        </div>

        

        <div class="form-group-add">
            <label>Ghi Chú</label>
            <textarea name="Note" rows="4" class="form-control-add form-control"><?= htmlspecialchars($order['Note']) ?></textarea>
        </div>

        <div class="button-group-add">
            <button type="submit" name="submit" class="btn btn-success">Cập nhật</button>
            <a href="?quanlydonhang" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>
