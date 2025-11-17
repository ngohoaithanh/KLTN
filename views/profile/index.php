<?php
// FILE: views/profile/index.php

// Lấy thông tin người dùng hiện tại từ SESSION
$user_id = $_SESSION['user_id'] ?? 0; // Đảm bảo bạn lưu user_id vào SESSION khi đăng nhập
$user_name = $_SESSION['user'] ?? 'N/A';
$user_email = $_SESSION['email'] ?? 'N/A'; // Lấy email từ SESSION
?>
<div class="container-fluid" style="margin-top:20px;"> 
<h1 class="h3 mb-4 text-gray-800">Hồ sơ cá nhân</h1>

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
                    
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user_name) ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_email) ?>">
                    </div>
                    <button type="submit" id="btn-update-info" class="btn btn-primary">
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
                    <button type="submit" id="btn-update-pass" class="btn btn-danger">
                        Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const formInfo = document.getElementById('form-update-info');
    const formPass = document.getElementById('form-update-password');
    const infoAlert = document.getElementById('info-alert');
    const passAlert = document.getElementById('pass-alert');
    const btnInfo = document.getElementById('btn-update-info');
    const btnPass = document.getElementById('btn-update-pass');

    // Hàm hiển thị thông báo
    function showAlert(alertElement, message, isSuccess) {
        alertElement.textContent = message;
        alertElement.classList.remove('d-none', isSuccess ? 'alert-danger' : 'alert-success');
        alertElement.classList.add(isSuccess ? 'alert-success' : 'alert-danger');
    }

    // Xử lý Form 1: Cập nhật Thông tin
    formInfo.addEventListener('submit', async function(e) {
        e.preventDefault();
        btnInfo.disabled = true;
        btnInfo.textContent = 'Đang xử lý...';
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/user/update_profile.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert(infoAlert, result.message, true);
                // Cập nhật tên trên thanh Topbar (nếu cần)
                // document.querySelector('#userDropdown .small').textContent = formData.get('full_name');
                alert('Cập nhật thành công! Vui lòng đăng nhập lại để thấy thay đổi.');
                window.location.href = '?logout'; // Buộc đăng xuất
            } else {
                showAlert(infoAlert, result.error, false);
            }

        } catch (error) {
            showAlert(infoAlert, 'Lỗi kết nối. Vui lòng thử lại.', false);
        } finally {
            btnInfo.disabled = false;
            btnInfo.textContent = 'Lưu thay đổi';
        }
    });

    // Xử lý Form 2: Cập nhật Mật khẩu
    formPass.addEventListener('submit', async function(e) {
        e.preventDefault();
        btnPass.disabled = true;
        btnPass.textContent = 'Đang xử lý...';
        
        const formData = new FormData(this);

        try {
            const response = await fetch('api/user/update_profile.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert(passAlert, result.message + ' Vui lòng đăng nhập lại.', true);
                // Đợi 2 giây rồi tự động đăng xuất
                setTimeout(() => {
                    window.location.href = '?logout';
                }, 2000);
            } else {
                showAlert(passAlert, result.error, false);
            }

        } catch (error) {
            showAlert(passAlert, 'Lỗi kết nối. Vui lòng thử lại.', false);
        } finally {
            btnPass.disabled = false;
            btnPass.textContent = 'Đổi mật khẩu';
        }
    });

});
</script>