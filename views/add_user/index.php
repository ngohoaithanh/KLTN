<?php
// FILE: views/add_user/index.php

// 1. LẤY DỮ LIỆU BAN ĐẦU
include_once('controllers/cUser.php');
$p = new controlNguoiDung();
include_once('config/env.php');

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
        .avatar-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }
        #avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .upload-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #007bff;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
        }
        .upload-icon:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="add-form-container">
    <h2 class="add-form-title">Thêm Người Dùng Mới</h2>
    <form method="POST" id="add-user-form">

        <div class="form-group text-center">
            <div class="avatar-wrapper">
                <img id="avatar-preview" src="views/img/avt.png" alt="Avatar Preview">
                <label for="avatar-input" class="upload-icon" title="Chọn ảnh">
                    <i class="fas fa-camera"></i>
                </label>
            </div>
            <input type="file" id="avatar-input" accept="image/*" style="display: none;">
            <input type="hidden" name="avatar_url" id="avatar-url-hidden">
            <small class="text-muted">Nhấn vào icon máy ảnh để tải ảnh lên</small>
        </div>
        <div class="form-group"><label>Họ tên</label><input type="text" name="username" class="form-control" required></div>
        <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" class="form-control" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        
        <div class="form-group">
            <label>Chức vụ</label>
            <select name="role" id="role-selector" class="form-control" required>
                <option value="">-- Chọn chức vụ --</option>
                <option value="2" <?= $default_role == 2 ? 'selected' : '' ?>>Quản lý</option>
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

        <div class="form-group"><label>Ghi chú</label><textarea name="note" rows="4" class="form-control" placeholder="Nhập ghi chú nếu có..."></textarea></div>

        <div class="btn-group">
            <button type="submit" name="submit" class="btn btn-primary">Thêm người dùng</button>
            <a href="?quanlyuser" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

<script>
    const CLOUD_NAME = "<?php echo CLOUDINARY_CLOUD_NAME; ?>";
    const UPLOAD_PRESET = "<?php echo CLOUDINARY_UPLOAD_PRESET; ?>";

    const CLOUDINARY_URL = `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`;

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

        // --- 2. LOGIC UPLOAD CLOUDINARY ---
        const avatarInput = document.getElementById('avatar-input');
        const avatarPreview = document.getElementById('avatar-preview');
        const form = document.getElementById('add-user-form');
        const submitBtn = document.querySelector('button[name="submit"]');
        let selectedFile = null;

        // 2a. Xem trước ảnh
        if(avatarInput) {
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // 1. Kiểm tra định dạng (Phải là ảnh)
                    // Các loại ảnh phổ biến: image/jpeg, image/png, image/gif, image/webp
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Lỗi: Vui lòng chỉ chọn file hình ảnh (.jpg, .png, .gif)!');
                        this.value = ''; // Reset input
                        selectedFile = null; // Xóa file đã chọn
                        avatarPreview.src = 'views/img/avt.png'; // Trả về ảnh mặc định
                        return; // Dừng lại
                    }

                    // 2. Kiểm tra kích thước (Ví dụ: Không quá 5MB)
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (file.size > maxSize) {
                        alert('Lỗi: Kích thước ảnh quá lớn! Vui lòng chọn ảnh dưới 5MB.');
                        this.value = '';
                        selectedFile = null;
                        return;
                    }

                    selectedFile = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if(avatarPreview) avatarPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // 2b. Xử lý Submit
        if(form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault(); 

                const originalBtnText = submitBtn.innerText;
                submitBtn.innerText = 'Đang xử lý...';
                submitBtn.disabled = true;

                try {
                    let avatarUrl = '';

                    // Bước A: Nếu có ảnh -> Upload lên Cloudinary
                    if (selectedFile) {
                        submitBtn.innerText = 'Đang tải ảnh lên...';
                        
                        const formData = new FormData();
                        formData.append('file', selectedFile);
                        formData.append('upload_preset', UPLOAD_PRESET);
                        formData.append('folder', 'avatars');

                        const cloudResponse = await fetch(CLOUDINARY_URL, {
                            method: 'POST',
                            body: formData
                        });

                        if (!cloudResponse.ok) {
                            throw new Error('Lỗi khi upload ảnh lên Cloudinary');
                        }

                        const cloudData = await cloudResponse.json();
                        avatarUrl = cloudData.secure_url; // Lấy URL ảnh
                    }

                    // Bước B: Gán URL vào input ẩn
                    const hiddenInput = document.getElementById('avatar-url-hidden');
                    if(hiddenInput) hiddenInput.value = avatarUrl;

                    // Bước C: Gửi dữ liệu về Server PHP
                    submitBtn.innerText = 'Đang lưu thông tin...';
                    
                    const formDataPHP = new FormData(form);
                    // Đảm bảo URL được gửi đi (phòng khi input hidden chưa ăn)
                    formDataPHP.set('avatar_url', avatarUrl); 

                    const response = await fetch('api/user/add_user.php', {
                        method: 'POST',
                        body: formDataPHP
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Nếu là Shipper -> Thêm thông tin xe (nếu cần gọi API riêng)
                        if (formDataPHP.get('role') == '6' && result.new_user_id) {
                             // Gọi API addShipperVehicle tại đây nếu cần
                             // (Hoặc tốt nhất là tích hợp logic này vào trong add_user.php luôn)
                        }

                        alert('Thêm người dùng thành công!');
                        window.location.href = '?quanlyuser';
                    } else {
                        alert('Lỗi: ' + (result.error || result.message));
                        submitBtn.innerText = originalBtnText;
                        submitBtn.disabled = false;
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra: ' + error.message);
                    submitBtn.innerText = originalBtnText;
                    submitBtn.disabled = false;
                }
            });
        }

    });
</script>
</body>
</html>