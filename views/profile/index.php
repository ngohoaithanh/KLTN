<?php
// FILE: views/profile/index.php (Bố cục mới: Ảnh ở giữa)

$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['user'] ?? 'N/A';
$user_email = $_SESSION['email'] ?? 'N/A';
$user_avatar = (isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])) ? $_SESSION['avatar'] : 'views/img/avt.png';
?>

<style>
    /* CSS cho khung ảnh mới */
    .profile-header-card {
        background-color: #fff;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    
    .profile-avatar-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 15px; /* Căn giữa */
    }
    
    #profile-avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.15);
    }
    
    .profile-upload-icon {
        position: absolute;
        bottom: 5px;
        right: 10px;
        background: #4e73df;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid white;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .profile-upload-icon:hover {
        background: #224abe;
        transform: scale(1.1);
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 5px;
    }
    
    .profile-email {
        color: #888;
        font-size: 1rem;
    }
</style>

<h1 class="h3 mb-4 text-gray-800 text-center" style="margin-top: 20px;">Hồ sơ cá nhân</h1>

<div class="row">
    <div class="col-12">
        <div class="profile-header-card">
            <div class="profile-avatar-wrapper">
                <img id="profile-avatar-preview" src="<?= htmlspecialchars($user_avatar) ?>" alt="Avatar">
                
                <label for="profile-avatar-input" class="profile-upload-icon" title="Đổi ảnh đại diện">
                    <i class="fas fa-camera"></i>
                </label>
            </div>
            
            <input type="file" id="profile-avatar-input" accept="image/*" style="display: none;">
            
            <div class="profile-name"><?= htmlspecialchars($user_name) ?></div>
            <div class="profile-email"><?= htmlspecialchars($user_email) ?></div>
            
            <div class="mt-2 text-muted small">Nhấn vào icon máy ảnh để thay đổi</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
            </div>
            <div class="card-body">
                <form id="form-update-info">
                    <div id="info-alert" class="alert d-none" role="alert"></div>
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    
                    <input type="hidden" name="avatar_url" id="profile-avatar-url-hidden" value="<?= htmlspecialchars($user_avatar) ?>">

                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user_name) ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_email) ?>">
                    </div>
                    <button type="submit" id="btn-update-info" class="btn btn-primary btn-block">
                        Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Đổi mật khẩu</h6>
            </div>
            <div class="card-body">
                <form id="form-update-password">
                    <div id="pass-alert" class="alert d-none" role="alert"></div>
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">

                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="old_password" class="form-control" placeholder="Nhập mật khẩu cũ" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới" required>
                    </div>
                    <button type="submit" id="btn-update-pass" class="btn btn-danger btn-block">
                        Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // --- CẤU HÌNH CLOUDINARY ---
    const CLOUD_NAME = "dbaeafw6z"; 
    const UPLOAD_PRESET = "user_avt"; 
    const CLOUDINARY_URL = `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`;

document.addEventListener('DOMContentLoaded', function() {
    
    const formInfo = document.getElementById('form-update-info');
    const formPass = document.getElementById('form-update-password');
    const infoAlert = document.getElementById('info-alert');
    const passAlert = document.getElementById('pass-alert');
    const btnInfo = document.getElementById('btn-update-info');
    const btnPass = document.getElementById('btn-update-pass');
    
    const avatarInput = document.getElementById('profile-avatar-input');
    const avatarPreview = document.getElementById('profile-avatar-preview');
    let selectedFile = null;

    function showAlert(alertElement, message, isSuccess) {
        alertElement.textContent = message;
        alertElement.classList.remove('d-none', 'alert-danger', 'alert-success');
        alertElement.classList.add(isSuccess ? 'alert-success' : 'alert-danger');
        alertElement.classList.remove('d-none');
    }

    // 1. Xử lý xem trước ảnh
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            selectedFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // 2. Xử lý Form 1: Cập nhật Thông tin & Upload Ảnh
    formInfo.addEventListener('submit', async function(e) {
        e.preventDefault();
        const originalText = btnInfo.textContent;
        btnInfo.disabled = true;
        btnInfo.textContent = 'Đang xử lý...';
        
        try {
            // Bước A: Upload ảnh lên Cloudinary (Nếu có chọn ảnh mới)
            if (selectedFile) {
                btnInfo.textContent = 'Đang tải ảnh lên...';
                const formData = new FormData();
                formData.append('file', selectedFile);
                formData.append('upload_preset', UPLOAD_PRESET);
                formData.append('folder', 'avatars');

                const cloudResponse = await fetch(CLOUDINARY_URL, {
                    method: 'POST', body: formData
                });
                
                if (!cloudResponse.ok) throw new Error('Lỗi khi upload ảnh');
                
                const cloudData = await cloudResponse.json();
                // Cập nhật URL mới vào input ẩn
                document.getElementById('profile-avatar-url-hidden').value = cloudData.secure_url;
            }

            // Bước B: Gửi dữ liệu về Server PHP
            btnInfo.textContent = 'Đang lưu thông tin...';
            const formDataPHP = new FormData(formInfo);
            
            const response = await fetch('api/user/update_profile.php', {
                method: 'POST',
                body: formDataPHP
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert(infoAlert, result.message, true);
                
                // Cập nhật Header ngay lập tức
                const headerAvatar = document.querySelector('.nav-user-info img');
                const headerName = document.querySelector('.nav-user-info strong');
                const profileName = document.querySelector('.profile-name'); // Cập nhật tên ở khối ảnh

                if (headerAvatar && document.getElementById('profile-avatar-url-hidden').value) {
                    headerAvatar.src = document.getElementById('profile-avatar-url-hidden').value;
                }
                if (headerName) headerName.textContent = formDataPHP.get('full_name');
                if (profileName) profileName.textContent = formDataPHP.get('full_name');
                
                selectedFile = null; 

            } else {
                showAlert(infoAlert, result.error, false);
            }

        } catch (error) {
            console.error(error);
            showAlert(infoAlert, 'Lỗi kết nối hoặc lỗi upload. Vui lòng thử lại.', false);
        } finally {
            btnInfo.disabled = false;
            btnInfo.textContent = originalText;
        }
    });

    // 3. Xử lý Form 2: Đổi mật khẩu (Giữ nguyên)
    formPass.addEventListener('submit', async function(e) {
        e.preventDefault();
        btnPass.disabled = true;
        btnPass.textContent = 'Đang xử lý...';
        const formData = new FormData(this);
        try {
            const response = await fetch('api/user/update_profile.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showAlert(passAlert, result.message + ' Đang đăng xuất...', true);
                setTimeout(() => { window.location.href = '?logout'; }, 1500);
            } else {
                showAlert(passAlert, result.error, false);
            }
        } catch (error) {
            showAlert(passAlert, 'Lỗi kết nối.', false);
        } finally {
            btnPass.disabled = false;
            btnPass.textContent = 'Đổi mật khẩu';
        }
    });

});
</script>