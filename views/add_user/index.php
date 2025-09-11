<?php
include_once('controllers/cUser.php');
$p = new controlNguoiDung();

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $role     = trim($_POST['role']);
    $password = trim($_POST['password']);
    $note = trim($_POST['note']);
    
    $warehouse_id = trim($_POST['warehouse_id']);
    if ($warehouse_id === "") {
        $warehouse_id = null;
    }

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
        $data = [
            'username' => $username,
            'phone'    => $phone,
            'email'    => $email,
            'role'     => $role,
            'warehouse_id' => $warehouse_id,
            'password' => $password,
            'note'     => $note
        ];
        $result = $p->addUser($data);

        if ($result && isset($result['success']) && $result['success']) {
            echo "<script>alert('Thêm nhân viên thành công!'); window.location.href='?quanlyuser';</script>";
            exit();
        } else {
            $errorMessage = isset($result['error']) ? $result['error'] : 'Không rõ nguyên nhân';
            echo "<script>alert('Thêm nhân viên thất bại: $errorMessage');</script>";
        }
    }
}

include_once('controllers/cWarehouse.php');
// Khởi tạo đối tượng và lấy dữ liệu
$p = new controlWarehouse();
$tblSP = $p->getAllWarehouse(); // Nếu không thì lấy tất cả

$warehouses = [];
if ($tblSP) {
    foreach ($tblSP as $row) {
            $warehouses[] = [
                'id' => $row['ID']?? '',
                'name' => $row['Name']?? '',
            ];
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Nhân Viên</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="add-staff-form" style="margin-top: 50px;">
    <h2 class="add-staff-title">Thêm Nhân Viên</h2>
    <form method="POST">
        <div class="form-group-add">
            <label>Họ tên</label>
            <input type="text" name="username" class="form-control-add" required>
        </div>
        <div class="form-group-add">
            <label>Số điện thoại</label>
            <input type="text" name="phone" class="form-control-add" required>
        </div>
        <div class="form-group-add">
            <label>Email</label>
            <input type="email" name="email" class="form-control-add" required>
        </div>
        <div class="form-group-add">
            <label>Chức vụ</label>
            <select name="role" class="form-control-add" required>
                <option value="">-- Chọn chức vụ --</option>
                <option value="2">Quản lý</option>
                <option value="3">Nhân viên tiếp nhận</option>
                <option value="4">Quản lý kho</option>
                <option value="5">Kế toán</option>
                <option value="6">Shipper</option>
                <option value="7">Khách hàng</option>
            </select>
        </div>
        <div class="form-group-add">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control-add" required>
        </div>
        <div class="form-group-add">
            <label>Nơi làm việc</label>
            <select name="warehouse_id" class="form-control-add" >
                <option value="">-- Chọn nơi làm việc --</option>
                <?php foreach ($warehouses as $w): ?>
                <option value="<?= htmlspecialchars($w['id']) ?>"><?= htmlspecialchars($w['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group-add">
            <label>Ghi chú</label>
            <textarea name="note" rows="4" class="form-control-add" placeholder="Nhập ghi chú nếu có..."></textarea>
        </div>    
        <div class="button-group-add">
            <button type="submit" name="submit" class="btn-add">Thêm nhân viên</button>
            <a href="?quanlyuser" class="btn-back">Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>
