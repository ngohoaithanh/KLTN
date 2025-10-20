<?php
// FILE: views/add_user/index.php

// 1. LẤY DỮ LIỆU BAN ĐẦU
include_once('controllers/cUser.php');
$p = new controlNguoiDung();

// Lấy vai trò mặc định từ URL nếu có (khi click nút "Thêm Shipper")
$default_role = isset($_GET['role']) ? intval($_GET['role']) : '';

// 2. XỬ LÝ KHI FORM ĐƯỢỢC SUBMIT
if (isset($_POST['submit'])) {
    // Lấy dữ liệu user
    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $role     = trim($_POST['role']);
    $password = trim($_POST['password']);
    $note     = trim($_POST['note']);
    // $warehouse_id = !empty(trim($_POST['warehouse_id'])) ? trim($_POST['warehouse_id']) : null;

    // Validate dữ liệu
    $errors = [];
    if (empty($username)) $errors[] = "Họ tên không được để trống.";
    if (empty($phone)) $errors[] = "Số điện thoại không được để trống.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
    if (empty($role)) $errors[] = "Chức vụ không được để trống.";
    if (empty($password)) $errors[] = "Mật khẩu không được để trống.";

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    } else {
        // Chuẩn bị dữ liệu để thêm user
        $data = [
            'username' => $username, 'phone' => $phone, 'email' => $email, 'role' => $role,
            'password' => $password, 'note' => $note
        ];
        
        // Gọi controller để thêm user
        $result = $p->addUser($data);

        if ($result && isset($result['success']) && $result['success']) {
            // Lấy ID của user vừa được tạo (QUAN TRỌNG: API của bạn cần trả về 'new_user_id')
            $newUserId = $result['new_user_id'] ?? null;

            // NẾU THÊM SHIPPER THÀNH CÔNG, TIẾN HÀNH THÊM THÔNG TIN XE
            if ($newUserId && $role == 6 && !empty($_POST['license_plate'])) {
                include_once('models/mUser.php');
                $mUser = new modelNguoiDung();
                $mUser->addShipperVehicle(
                    $newUserId,
                    $_POST['license_plate'],
                    $_POST['vehicle_model'] ?? ''
                );
            }

            echo "<script>alert('Thêm người dùng thành công!'); window.location.href='?quanlyuser';</script>";
            exit();
        } else {
            $errorMessage = $result['error'] ?? 'Không rõ nguyên nhân';
            echo "<script>alert('Thêm người dùng thất bại: $errorMessage');</script>";
        }
    }
}

// 3. LẤY DANH SÁCH KHO (giữ nguyên)
// include_once('controllers/cWarehouse.php');
// $cWarehouse = new controlWarehouse();
// $warehouses_data = $cWarehouse->getAllWarehouse();
// $warehouses = [];
// if ($warehouses_data) {
//     foreach ($warehouses_data as $row) {
//         $warehouses[] = ['id' => $row['ID'] ?? '', 'name' => $row['Name'] ?? ''];
//     }
// }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Người Dùng Mới</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .add-form-container { max-width: 600px; margin: 50px auto; padding: 25px; background: #f8f9fa; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .add-form-title { text-align: center; margin-bottom: 25px; }
        .form-group label { font-weight: 500; }
        .btn-group { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
<div class="add-form-container">
    <h2 class="add-form-title">Thêm Người Dùng Mới</h2>
    <form method="POST">
        <div class="form-group"><label>Họ tên</label><input type="text" name="username" class="form-control" required></div>
        <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" class="form-control" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        
        <div class="form-group">
            <label>Chức vụ</label>
            <select name="role" id="role-selector" class="form-control" required>
                <option value="">-- Chọn chức vụ --</option>
                <option value="2" <?= $default_role == 2 ? 'selected' : '' ?>>Quản lý</option>
                <option value="3" <?= $default_role == 3 ? 'selected' : '' ?>>Nhân viên tiếp nhận</option>
                <option value="4" <?= $default_role == 4 ? 'selected' : '' ?>>Quản lý kho</option>
                <option value="5" <?= $default_role == 5 ? 'selected' : '' ?>>Kế toán</option>
                <option value="6" <?= $default_role == 6 ? 'selected' : '' ?>>Shipper</option>
                <option value="7" <?= $default_role == 7 ? 'selected' : '' ?>>Khách hàng</option>
            </select>
        </div>

        <div id="shipper-fields" style="display: none;">
            <hr>
            <h5 class="text-primary">Thông tin phương tiện (Bắt buộc cho Shipper)</h5>
            <div class="form-group">
                <label>Biển số xe</label>
                <input type="text" name="license_plate" id="license_plate_input" class="form-control">
            </div>
            <div class="form-group">
                <label>Loại xe (VD: Honda Wave)</label>
                <input type="text" name="vehicle_model" class="form-control">
            </div>
            <hr>
        </div>

        <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" class="form-control" required></div>
        
        <!-- <div class="form-group">
            <label>Nơi làm việc</label>
            <select name="warehouse_id" class="form-control" >
                <option value="">-- Chọn nơi làm việc --</option>
                <?php foreach ($warehouses as $w): ?>
                <option value="<?= htmlspecialchars($w['id']) ?>"><?= htmlspecialchars($w['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div> -->

        <div class="form-group"><label>Ghi chú</label><textarea name="note" rows="4" class="form-control" placeholder="Nhập ghi chú nếu có..."></textarea></div>

        <div class="btn-group">
            <button type="submit" name="submit" class="btn btn-primary">Thêm người dùng</button>
            <a href="?quanlyuser" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelector = document.getElementById('role-selector');
        const shipperFields = document.getElementById('shipper-fields');
        const licensePlateInput = document.getElementById('license_plate_input');

        function toggleShipperFields() {
            if (roleSelector.value == '6') { // Nếu chọn vai trò là Shipper
                shipperFields.style.display = 'block';
                licensePlateInput.required = true; // Bắt buộc nhập biển số xe
            } else {
                shipperFields.style.display = 'none';
                licensePlateInput.required = false; // Không bắt buộc
            }
        }

        toggleShipperFields(); // Chạy lần đầu khi tải trang
        roleSelector.addEventListener('change', toggleShipperFields); // Lắng nghe sự kiện thay đổi
    });
</script>
</body>
</html>