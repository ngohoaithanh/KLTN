<?php
$FILE_ID = "1gAhsEe1w9s07Fvs64G3lT1bPNn-WfIyH";

$APK_URL = "https://drive.google.com/uc?export=download&id=" . $FILE_ID;
$QR_URL  = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($APK_URL);
?>

<div class="logismart-download-page">
    <style>
        .logismart-download-page {
            background: #d7e4edff;
            min-height: calc(100vh - 120px); /* trừ header/footer nếu có */
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
            padding: 15px;
        }
        .ls-download-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.20);
            overflow: hidden;
            max-width: 550px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }
        .ls-download-visual {
            flex: 1;
            min-width: 280px;
            background: url('https://cdn.dribbble.com/users/1615584/screenshots/15710330/media/3b0a47222908df732570b0df8664637d.jpg?compress=1&resize=800x600') center/cover;
            position: relative;
        }
        .ls-download-visual::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(52,152,219,0.15);
        }
        .ls-download-content {
            flex: 1;
            padding: 40px 30px;
            min-width: 280px;
            text-align: center;
        }
        .ls-app-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 12px;
        }

        .ls-app-logo img {
            width: 100%;
            height: 100%;
            border-radius: 20%;
            object-fit: cover;
        }
        .ls-app-name {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .ls-download-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #3ddc84;
            color: #fff;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            margin: 12px auto;
            transition: 0.2s;
            max-width: 260px;
        }
        .ls-download-btn:hover {
            transform: translateY(-3px);
            background-color: #32b36b;
            text-decoration: none;
            color: white;
        }
        .ls-download-btn i {
            font-size: 22px;
            margin-right: 12px;
        }
        .ls-btn-text small {
            display: block;
            font-size: 11px;
            opacity: 0.8;
        }
        .ls-btn-text span {
            font-size: 16px;
            font-weight: bold;
        }
        .ls-qr-section {
            margin-top: 24px;
        }
        .ls-qr-img {
            width: 130px;
            height: 130px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 8px;
            background: #fff;
        }
        .ls-back-link {
            margin-top: 20px;
            display: inline-block;
            color: #555;
            font-size: 14px;
        }
        .ls-back-link:hover {
            text-decoration: none;
            color: #000;
        }
        .ls-install-guide {
            margin-top: 18px;
            font-size: 13px;
            color: #555;
            text-align: left;
        }
        .ls-install-guide ol {
            padding-left: 18px;
            margin-bottom: 6px;
        }
        .ls-install-guide li {
            margin-bottom: 4px;
        }

        @media (max-width: 768px) {
            .ls-download-content {
                padding: 30px 20px;
            }
        }
    </style>

    <div class="ls-download-card">
        <div class="ls-download-visual"></div>

        <div class="ls-download-content">
            <div class="ls-app-logo">
                <img src="views/img/favicon.png" alt="LOGISMART Logo">
            </div>

            <h1 class="ls-app-name">LOGISMART</h1>

            <p class="text-muted mb-3">
                Ứng dụng quản lý giao hàng dành cho Shipper.<br>
                Tải xuống và cài đặt nhanh chóng.
            </p>

            <!-- NÚT TẢI APK TỪ GOOGLE DRIVE -->
            <a href="<?= htmlspecialchars($APK_URL) ?>" class="ls-download-btn" target="_blank" rel="noopener">
                <i class="fab fa-android"></i>
                <div class="ls-btn-text">
                    <small>Tải xuống từ Google Drive</small>
                    <span>LOGISMART APK</span>
                </div>
            </a>

            <!-- QR CODE -->
            <div class="ls-qr-section">
                <p class="mb-2"><strong>Quét mã để tải nhanh</strong></p>
                <img src="<?= htmlspecialchars($QR_URL) ?>" class="ls-qr-img" alt="QR Code tải LOGISMART">
                <p class="small text-muted mt-2 mb-0">
                    Hỗ trợ Android 8.0 trở lên
                </p>
            </div>

            <!-- HƯỚNG DẪN CÀI ĐẶT NGẮN GỌN -->
            <div class="ls-install-guide">
                <strong>Cách cài đặt:</strong>
                <ol>
                    <li>Nhấn nút <strong>"LOGISMART APK"</strong> để mở file trên Google Drive.</li>
                    <li>Chọn <strong>Tải xuống (Download)</strong> để lưu file APK về máy.</li>
                    <li>Mở file trong ứng dụng <em>Tải xuống / Files / Quản lý tệp</em>.</li>
                    <li>Nếu máy báo “Không rõ nguồn gốc”, chọn <strong>Cho phép cài đặt từ nguồn này</strong>.</li>
                    <li>Nhấn <strong>Cài đặt (Install)</strong> và mở ứng dụng LOGISMART.</li>
                </ol>
            </div>

            <a href="index.php" class="ls-back-link">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
</div>
