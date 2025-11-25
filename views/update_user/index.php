<?php
// FILE: views/update_user/index.php (Phiên bản Cloudinary)

// 1. LẤY DỮ LIỆU NGƯỜI DÙNG HIỆN TẠI (PHP thuần để render form)
include_once('controllers/cUser.php');
$p = new controlNguoiDung();
$user = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user = $p->getUserById($id); 
}

if (!$user) {
    echo "<script>alert('Không tìm thấy người dùng!'); window.location.href='?quanlyuser';</script>";
    exit();
}


// Xác định ảnh đại diện hiện tại
$currentAvatar = !empty($user['Avatar']) ? $user['Avatar'] : 'views/img/avt.png';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .update-form-container { max-width: 600px; margin: 50px auto; padding: 25px; background: #f8f9fa; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .update-form-title { text-align: center; margin-bottom: 25px; }
        .form-group label { font-weight: 500; }
        .btn-group { display: flex; justify-content: space-between; }
        
        /* Style cho Avatar */
        .avatar-wrapper {
            position: relative; width: 120px; height: 120px; margin: 0 auto 20px;
        }
        #avatar-preview {
            width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
            border: 3px solid #e9ecef; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .upload-icon {
            position: absolute; bottom: 0; right: 0;
            background: #ffc107; /* Màu vàng cho nút sửa */
            color: #333;
            width: 35px; height: 35px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; border: 2px solid white;
        }
        .upload-icon:hover { background: #e0a800; }
    </style>
</head>
<body>

<div class="update-form-container">
    <h3 class="update-form-title">Cập nhật: <?= htmlspecialchars($user['Username']) ?></h3>
    
    <form method="POST" id="update-user-form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['ID']) ?>">

        <div class="form-group text-center">
            <div class="avatar-wrapper">
                <img id="avatar-preview" src="<?= htmlspecialchars($currentAvatar) ?>" alt="Avatar">
                <label for="avatar-input" class="upload-icon" title="Đổi ảnh">
                    <i class="fas fa-pen"></i>
                </label>
            </div>
            <input type="file" id="avatar-input" accept="image/*" style="display: none;">
            
            <input type="hidden" name="avatar_url" id="avatar-url-hidden" value="<?= htmlspecialchars($user['Avatar'] ?? '') ?>">
        </div>

        <div class="form-group"><label>Họ tên</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['Username']) ?>" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['Email']) ?>" required></div>
        <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['PhoneNumber']) ?>" required></div>
        
        <div class="form-group"><label>Chức vụ</label>
            <select name="role" id="role-selector" class="form-control">
                <option value="">-- Chọn chức vụ --</option>
                <option value="2" <?= $user['Role'] == 2 ? 'selected' : '' ?>>Quản lý</option>
                <option value="5" <?= $user['Role'] == 5 ? 'selected' : '' ?>>Kế toán</option>
                <option value="6" <?= $user['Role'] == 6 ? 'selected' : '' ?>>Shipper</option>
                <option value="7" <?= $user['Role'] == 7 ? 'selected' : '' ?>>Khách hàng</option>
            </select>
        </div>
        
        <div id="shipper-fields" style="display: none;">
            <hr>
            <h5 class="text-primary">Thông tin phương tiện</h5>
            <div class="form-group">
                <label>Biển số xe</label>
                <input type="text" name="license_plate" id="license_plate_input" class="form-control" value="<?= htmlspecialchars($user['license_plate'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Loại xe</label>
                <input type="text" name="vehicle_model" class="form-control" value="<?= htmlspecialchars($user['vehicle_model'] ?? '') ?>">
            </div>
            <hr>
        </div>
        
        <div class="form-group"><label>Mật khẩu mới (nếu đổi)</label><input type="password" name="password" class="form-control" placeholder="Để trống nếu không thay đổi"></div>
        <div class="form-group"><label>Ghi chú</label><textarea name="note" rows="4" class="form-control"><?= htmlspecialchars($user['Note']) ?></textarea></div>

        <div class="btn-group">
            <button type="submit" name="submit" class="btn btn-success">Lưu thay đổi</button>
            <a href="?quanlyuser" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

<script>
    // CẤU HÌNH CLOUDINARY (Copy từ trang Add User sang)
    const CLOUD_NAME = "dbaeafw6z"; // <-- Đảm bảo đúng tên cloud
    const UPLOAD_PRESET = "user_avt"; 
    const CLOUDINARY_URL = `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`;

    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. LOGIC ẨN/HIỆN TRƯỜNG SHIPPER
        const roleSelector = document.getElementById('role-selector');
        const shipperFields = document.getElementById('shipper-fields');
        const licensePlateInput = document.getElementById('license_plate_input');

        function toggleShipperFields() {
            if (roleSelector.value == '6') { 
                shipperFields.style.display = 'block';
                // licensePlateInput.required = true; // Có thể bỏ required khi update nếu không muốn bắt buộc
            } else {
                shipperFields.style.display = 'none';
                licensePlateInput.required = false;
            }
        }
        toggleShipperFields();
        roleSelector.addEventListener('change', toggleShipperFields);

        // 2. LOGIC UPLOAD & UPDATE
        const avatarInput = document.getElementById('avatar-input');
        const avatarPreview = document.getElementById('avatar-preview');
        const form = document.getElementById('update-user-form');
        const submitBtn = document.querySelector('button[name="submit"]');
        let selectedFile = null;

        // Khi chọn ảnh mới
        if(avatarInput) {
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    selectedFile = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result; // Hiện ảnh mới ngay
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // Khi Submit
        if(form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault(); 

                const originalBtnText = submitBtn.innerText;
                submitBtn.innerText = 'Đang xử lý...';
                submitBtn.disabled = true;

                try {
                    // Bước A: Nếu có ảnh mới -> Upload lên Cloudinary
                    if (selectedFile) {
                        submitBtn.innerText = 'Đang tải ảnh mới...';
                        
                        const formData = new FormData();
                        formData.append('file', selectedFile);
                        formData.append('upload_preset', UPLOAD_PRESET);
                        formData.append('folder', 'avatars'); // Gom vào thư mục

                        const cloudResponse = await fetch(CLOUDINARY_URL, {
                            method: 'POST',
                            body: formData
                        });

                        if (!cloudResponse.ok) {
                            throw new Error('Lỗi upload ảnh');
                        }

                        const cloudData = await cloudResponse.json();
                        // Gán URL mới vào input ẩn
                        document.getElementById('avatar-url-hidden').value = cloudData.secure_url;
                    }
                    // Nếu không chọn ảnh mới, input ẩn vẫn giữ URL cũ (đã set từ PHP)

                    // Bước B: Gửi dữ liệu về API
                    submitBtn.innerText = 'Đang lưu...';
                    const formDataPHP = new FormData(form); // Tự động lấy tất cả input (gồm cả avatar_url)

                    const response = await fetch('api/user/update_user.php', {
                        method: 'POST',
                        body: formDataPHP
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('Cập nhật thành công!');
                        window.location.href = '?quanlyuser';
                    } else {
                        alert('Lỗi: ' + result.error);
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