<?php
include_once('controllers/cUser.php');
$p = new controlNguoiDung();
$tblSP = $p->getUserByRole(4);

$users = [];
if ($tblSP && isset($tblSP['success']) && $tblSP['success'] && isset($tblSP['data'])) {
    foreach ($tblSP['data'] as $row) {
        $users[] = [
            'id' => $row['ID'] ?? '',
            'name' => $row['Username'] ?? '',
        ];
    }
}

include_once('controllers/cWarehouse.php');
$p2 = new controlWarehouse();
if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $address    = trim($_POST['address']);
    $capacity    = trim($_POST['capacity']);
    $manager_id     = trim($_POST['manager_id']);
    $operation_status = trim($_POST['operation_status']);
    


    $errors = [];

    if (empty($name)) $errors[] = "Tên kho không được để trống";
    if (empty($address)) $errors[] = "Địa chỉ không được để trống.";
    if (empty($manager_id)) $errors[] = "Quản lý không được để trống.";
    if (empty($operation_status)) $errors[] = "Tình trạng không được để trống.";

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    } else {
        $data = [
            'name' => $name,
            'address'    => $address,
            'capacity'    => $capacity,
            'manager_id'     => $manager_id,
            'operation_status' => $operation_status
        ];
        $result = $p2->addWarehouse($data);

        if ($result && isset($result['success']) && $result['success']) {
            echo "<script>alert('Tạo kho thành công!'); window.location.href='?quanlykho';</script>";
            exit();
        } else {
            $errorMessage = isset($result['error']) ? $result['error'] : 'Không rõ nguyên nhân';
            echo "<script>alert('Tạo kho thất bại: $errorMessage');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm kho mới</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
    body {
        background-color: #f5f6fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .wh-form-container {
        max-width: 600px;
        margin: 50px auto;
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .wh-form-title {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 700;
        font-size: 24px;
        color: #2c3e50;
    }

    .wh-form-group label {
        font-weight: 600;
        color: #34495e;
    }

    .form-control, select.form-group {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: none;
    }

    select.form-group {
        width: 100%;
        background-color: #fff;
    }

    .wh-btn-group {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 25px;
        font-weight: 500;
        border-radius: 8px;
    }

    .btn-secondary {
        padding: 10px 25px;
        font-weight: 500;
        border-radius: 8px;
    }

    .btn-primary:hover,
    .btn-secondary:hover {
        opacity: 0.9;
    }
</style>
</head>
<body>

<div class="wh-form-container">
    <h3 class="wh-form-title">Thêm kho mới</h3>
    <form method="POST" action="#">
        <div class="form-group wh-form-group">
            <label for="name">Tên kho</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên kho" required>
        </div>

        <div class="form-group wh-form-group">
            <label for="location">Địa chỉ</label>
            <input type="text" name="address" id="location" class="form-control" placeholder="Nhập địa chỉ kho" required>
        </div>

        <div class="form-group wh-form-group">
            <label for="location">Dung lượng kho</label>
            <input type="number" name="capacity" id="capacity" class="form-control" placeholder="Nhập số đơn hàng tối đa" required>
        </div>

        <div class="form-group wh-form-group">
            <label for="manager">Quản lý kho</label>
            <select name="manager_id" class="form-group wh-form-group" >
                <option value="">-- Chọn quản lý --</option>
                <?php foreach ($users as $u): ?>
                <option value="<?= htmlspecialchars($u['id']) ?>"><?= htmlspecialchars($u['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group wh-form-group">
            <label for="manager">Tình trạng kho</label>
            <select name="operation_status" class="form-group wh-form-group" >
                <option value="">-- Tình trạng kho --</option>
                <option value="active">Hoạt động</option>
                <option value="full">Đầy kho</option>
                <option value="paused">Tạm ngưng</option>
            </select>
        </div>

        <div class="wh-btn-group">
            <button type="submit" name="submit" class="btn btn-primary">Thêm kho</button>
            <a href="?quanlykho" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>
