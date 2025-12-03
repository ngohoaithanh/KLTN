<?php
$FILE_ID = "1zT-H5iKQ0JpDTMCTY6KoXvNVA2DjSqWU";

$APK_URL = "https://drive.google.com/uc?export=download&id=" . $FILE_ID;
$QR_URL  = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($APK_URL);
?>
<style>
    @keyframes pulse-fade-in {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    .intro-redirect-card {
        animation: pulse-fade-in 0.7s ease-out forwards;
        background: #fff; 
        padding: 40px; 
        border-radius: 8px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-top: 40px;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="intro-redirect-card">
                <div class="text-center p-4">
                    
                    <i class="fas fa-mobile-alt fa-4x" style="color: var(--primary-color); margin-bottom: 25px;"></i>
                    
                    <h2 class="h4" style="font-weight: 600; color: var(--dark-color);">
                        Chào mừng, <?= htmlspecialchars($_SESSION['user']) ?>!
                    </h2>
                    <p class="lead" style="font-size: 1.1rem;">Bạn đang ở Khu vực Quản trị Web.</p>
                    <hr class="my-4">

                    <?php if ($_SESSION['role'] == 6): // Tin nhắn cho Shipper ?>
                        <p style="font-size: 1.1rem;">
                            Các chức năng chính của Shipper (nhận đơn, cập nhật trạng thái, xem thu nhập) đều nằm trên **Ứng dụng Di động**.
                        </p>
                        <p>Vui lòng đăng nhập trên app QLGH Shipper để bắt đầu làm việc.</p>
                    
                    <?php else: // Tin nhắn cho Khách hàng (Role 7) ?>
                        <p style="font-size: 1.1rem;">
                            Các chức năng chính của Khách hàng (tạo đơn, theo dõi đơn) đều nằm trên **Ứng dụng Di động**.
                        </p>
                        <p>Vui lòng sử dụng app QLGH để quản lý đơn hàng của bạn.</p>
                    
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <strong>Tải ứng dụng tại đây:</strong><br>
                        <img src="<?= htmlspecialchars($QR_URL) ?>" class="ls-qr-img" alt="QR Code tải LOGISMART">
                        <p class="small text-muted">(Quét mã QR để tải ứng dụng)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>