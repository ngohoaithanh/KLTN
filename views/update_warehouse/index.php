<?php
include_once('controllers/cUser.php');
include_once('controllers/cWarehouse.php');

// Lấy danh sách quản lý
$userCtrl = new controlNguoiDung();
$managers = $userCtrl->getUserByRole(4); // Role 4: Quản lý kho

// Lấy thông tin kho cần chỉnh sửa
$warehouseCtrl = new controlWarehouse();
$warehouseData = [];

if (isset($_REQUEST['id'])) {
    $result = $warehouseCtrl->getWarehouseByID($_REQUEST['id']);
    if ($result && is_array($result)) {
        $warehouseData = $result;
    } else {
        $error = 'Không thể lấy thông tin kho';
        echo "<script>alert('$error'); window.location.href='?quanlykho';</script>";
        exit;
    }
} else {
    echo "<script>alert('Thiếu ID kho'); window.location.href='?quanlykho';</script>";
    exit;
}

// Xử lý cập nhật
if (isset($_POST['submit'])) {
    $data = [
        'id' => $_POST['id'],
        'name' => trim($_POST['name']),
        'address' => trim($_POST['address']),
        'capacity' => intval($_POST['capacity']),
        'manager_id' => intval($_POST['manager_id']),
        'operation_status' => $_POST['operation_status']
    ];
    
    $result = $warehouseCtrl->updateWarehouse($data);
    var_dump($result);
    if ($result && $result['success']) {
        echo "<script>alert('Cập nhật kho thành công!'); window.location.href='?quanlykho';</script>";
        exit;
    }else{
        $error = $result['message'] ?? 'Lỗi không xác định';
        echo "<script>alert('Cập nhật thất bại: $error');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa kho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --text-color: #5a5c69;
            --border-radius: 0.35rem;
        }
        
        body {
            background-color: var(--secondary-color);
            color: var(--text-color);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .warehouse-form-container {
            max-width: 650px;
            margin: 3rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
        }
        
        .warehouse-form-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #d1d3e2;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-secondary {
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .warehouse-form-container {
                padding: 1.5rem;
                margin: 1.5rem auto;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="warehouse-form-container">
            <h3 class="warehouse-form-title mb-4">Chỉnh sửa thông tin kho: <?= htmlspecialchars($warehouseData['Name'] ?? '') ?></h3>
            
            <form method="POST" action="#" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?= htmlspecialchars($_REQUEST['id']) ?>">
                
                <div class="mb-4">
                    <label for="name" class="form-label">Tên kho</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?= htmlspecialchars($warehouseData['Name'] ?? '') ?>" required>
                    <div class="invalid-feedback">Vui lòng nhập tên kho</div>
                </div>
                
                <div class="mb-4">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" id="address" name="address" class="form-control" 
                           value="<?= htmlspecialchars($warehouseData['Address'] ?? '') ?>" required>
                    <div class="invalid-feedback">Vui lòng nhập địa chỉ kho</div>
                </div>
                
                <div class="mb-4">
                    <label for="capacity" class="form-label">Dung lượng tối đa</label>
                    <input type="number" id="capacity" name="capacity" class="form-control" 
                           value="<?= htmlspecialchars($warehouseData['Capacity'] ?? '') ?>" required>
                    <div class="invalid-feedback">Vui lòng nhập dung lượng kho</div>
                </div>
                
                <div class="mb-4">
                    <label for="manager_id" class="form-label">Quản lý kho</label>
                    <select id="manager_id" name="manager_id" class="form-select" required>
                        <option value="">-- Chọn quản lý --</option>
                        <?php if (!empty($managers['data'])): ?>
                            <?php foreach ($managers['data'] as $manager): ?>
                            <option value="<?= $manager['ID'] ?>" 
                                <?= ($manager['ID'] == ($warehouseData['manager_id'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($manager['Username']) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">Vui lòng chọn quản lý kho</div>
                </div>
                
                <div class="mb-4">
                    <label for="operation_status" class="form-label">Tình trạng kho</label>
                    <select id="operation_status" name="operation_status" class="form-select" required>
                        <option value="active" <?= ($warehouseData['operation_status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="full" <?= ($warehouseData['operation_status'] ?? '') == 'full' ? 'selected' : '' ?>>Đầy kho</option>
                        <option value="paused" <?= ($warehouseData['operation_status'] ?? '') == 'paused' ? 'selected' : '' ?>>Tạm ngưng</option>
                    </select>
                    <div class="invalid-feedback">Vui lòng chọn tình trạng kho</div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="submit" class="btn btn-primary">Lưu thay đổi</button>
                    <a href="?quanlykho" class="btn btn-secondary">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script>
    // Xử lý validation form
    (function() {
        'use strict';
        
        // Lấy tất cả các form có class 'needs-validation'
        var forms = document.querySelectorAll('.needs-validation');
        
        // Lặp qua từng form và ngăn chặn submit nếu không hợp lệ
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
    })();
    </script> -->
</body>
</html>