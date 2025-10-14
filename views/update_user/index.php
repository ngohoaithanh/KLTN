<?php
// FILE: views/update_user/index.php

// 1. LẤY DỮ LIỆU BAN ĐẦU
include_once('controllers/cUser.php');
$p = new controlNguoiDung();
$user = null; // Khởi tạo biến user

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Hàm getUserById này sẽ được nâng cấp ở Bước 2 để lấy cả thông tin xe
    $user = $p->getUserById($id); 
}

if (!$user) {
    echo "<script>alert('Không tìm thấy người dùng!'); window.location.href='?quanlyuser';</script>";
    exit();
}

// 2. XỬ LÝ KHI FORM ĐƯỢC SUBMIT
if (isset($_POST['submit'])) {
    // Lấy dữ liệu user như cũ
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $warehouse_id = !empty(trim($_POST['warehouse_id'])) ? trim($_POST['warehouse_id']) : null;
    $password = $_POST['password'];
    $note = $_POST['note'];

    $data = [
        "id" => $id, "username" => $username, "email" => $email, "phone" => $phone,
        "role" => $role, "warehouse_id" => $warehouse_id, "note" => $note
    ];

    if (!empty($password)) {
        $data['password'] = $password;
    }

    // Cập nhật thông tin user như cũ
    $result = $p->updateUser($data);

    // *** PHẦN NÂNG CẤP: XỬ LÝ CẬP NHẬT THÔNG TIN XE ***
    // Nếu người dùng được cập nhật là Shipper (Role = 6)
    if ($role == 6) {
        // Cần tạo instance của Model để gọi hàm mới
        include_once('models/mUser.php');
        $mUser = new modelNguoiDung();
        $mUser->updateShipperVehicle(
            $id,
            $_POST['license_plate'] ?? '',
            $_POST['vehicle_model'] ?? ''
        );
    }
    
    if ($result && isset($result['success']) && $result['success'] == true) {
        echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='?quanlyuser';</script>";
    } else {
        $message = isset($result['message']) ? $result['message'] : 'Cập nhật thất bại!';
        echo "<script>alert('{$message}');</script>";
    }
}

// 3. LẤY DANH SÁCH KHO (giữ nguyên)
include_once('controllers/cWarehouse.php');
$cWarehouse = new controlWarehouse();
$warehouses_data = $cWarehouse->getAllWarehouse();
$warehouses = [];
if ($warehouses_data) {
    foreach ($warehouses_data as $row) {
        $warehouses[] = ['id' => $row['ID'] ?? '', 'name' => $row['Name'] ?? ''];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin người dùng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .update-form-container { max-width: 600px; margin: 50px auto; padding: 25px; background: #f8f9fa; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .update-form-title { text-align: center; margin-bottom: 25px; }
        .form-group label { font-weight: 500; }
        .btn-group { display: flex; justify-content: space-between; }
    </style>
</head>
<body>

<div class="update-form-container">
    <h3 class="update-form-title">Cập nhật thông tin: <?= htmlspecialchars($user['Username']) ?></h3>
    <form method="POST" action="#">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['ID']) ?>">

        <div class="form-group"><label>Họ tên</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['Username']) ?>" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['Email']) ?>" required></div>
        <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['PhoneNumber']) ?>" required></div>
        <div class="form-group"><label>Chức vụ</label><select name="role" class="form-control"><option value="">-- Chọn chức vụ --</option><option value="2" <?= $user['Role'] == 2 ? 'selected' : '' ?>>Quản lý</option><option value="3" <?= $user['Role'] == 3 ? 'selected' : '' ?>>Nhân viên tiếp nhận</option><option value="4" <?= $user['Role'] == 4 ? 'selected' : '' ?>>Quản lý kho</option><option value="5" <?= $user['Role'] == 5 ? 'selected' : '' ?>>Kế toán</option><option value="6" <?= $user['Role'] == 6 ? 'selected' : '' ?>>Shipper</option><option value="7" <?= $user['Role'] == 7 ? 'selected' : '' ?>>Khách hàng</option></select></div>
        <div class="form-group"><label>Nơi làm việc</label><select name="warehouse_id" class="form-control"><option value="">-- Chọn nơi làm việc --</option><?php foreach ($warehouses as $w): ?><option value="<?= $w['id'] ?>" <?= ($user['warehouse_id'] ?? null) == $w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?></option><?php endforeach; ?></select></div>
        
        <?php if ($user['Role'] == 6): // Chỉ hiển thị nếu là Shipper ?>
            <hr>
            <h5 class="text-primary">Thông tin phương tiện (Shipper)</h5>
            
            <div class="form-group">
                <label>Biển số xe</label>
                <input type="text" name="license_plate" class="form-control" 
                       value="<?= htmlspecialchars($user['license_plate'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Loại xe (VD: Honda Wave)</label>
                <input type="text" name="vehicle_model" class="form-control" 
                       value="<?= htmlspecialchars($user['vehicle_model'] ?? '') ?>">
            </div>
        <?php endif; ?>
        
        <hr>
        <div class="form-group"><label>Mật khẩu mới (nếu đổi)</label><input type="password" name="password" class="form-control" placeholder="Để trống nếu không thay đổi"></div>
        <div class="form-group"><label>Ghi chú</label><textarea name="note" rows="4" class="form-control"><?= htmlspecialchars($user['Note']) ?></textarea></div>

        <div class="btn-group">
            <button type="submit" name="submit" class="btn btn-success">Lưu thay đổi</button>
            <a href="?quanlyuser" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>