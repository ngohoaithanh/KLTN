<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

  <style>
    .footer-section {
      background-color: #214870;
      color: white;
      padding: 40px 20px 20px;
      margin-top: 2rem;
    }

    .footer-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      max-width: 1200px;
      margin: auto;
    }

    .footer-col {
      flex: 1 1 300px;
      margin-bottom: 20px;
      color: white;
    }

    .footer-title {
      margin-bottom: 15px;
      color: white;
      font-size: 18px;
    }

    .footer-text {
      font-size: 14px;
      line-height: 1.6;
      color: white;
    }

    .footer-links {
      list-style: none;
      padding: 0;
    }

    .footer-links li {
      margin-bottom: 10px;
    }

    .footer-links li a {
      color: white;
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer-links li a:hover {
      color: var(--primary-color);
    }

    .footer-bottom {
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      margin-top: 20px;
      padding-top: 10px;
    }

    .footer-bottom-text {
      font-size: 14px;
      color: white;
    }

    @media (max-width: 768px) {
      .footer-container {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <footer class="footer-section">
    <div class="footer-container">
      <div class="footer-col">
        <h4 class="footer-title">Về chúng tôi</h4>
        <p class="footer-text">
          Hệ thống giao hàng toàn diện, hỗ trợ quản lý đơn hàng và nhân viên dễ dàng.
        </p>
      </div>
      <div class="footer-col">
        <h4 class="footer-title">Liên kết</h4>
        <ul class="footer-links">
          <li><a href="index.php">Trang chủ</a></li>
          <li><a href="?quanlydonhang">Đơn hàng</a></li>
          <li><a href="?quanlyuser">Nhân viên</a></li>
          <li><a href="?dashboard">Báo cáo</a></li>
          <li>
              <a href="?download" style="color: #ffc107;">
                  <i class="fas fa-download" style="margin-right: 5px;"></i> Tải Ứng Dụng
              </a>
          </li>
        </ul>
      </div>
      <div class="footer-col">
        <h4 class="footer-title">Liên hệ</h4>
        <p class="footer-text">Email: thanhhuykks03@gmail.com</p>
        <p class="footer-text">Hotline: 1900 1234</p>
        <p class="footer-text">Địa chỉ: 66B, Nguyễn Sỹ Sách, P15, Tân Bình</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="footer-bottom-text">
        &copy; 2025 LOGISMART. All rights reserved.
      </p>
      
      <p class="footer-bottom-text" style="font-size: 13px; margin-top: 5px; opacity: 0.8;">
          Mọi vấn đề kỹ thuật vui lòng liên lạc qua email: 
          <a href="mailto:thanhhuykks03@gmail.com" style="color: #fff; text-decoration: underline;">thanhhuykks03@gmail.com</a>
      </p>
      </div>
  </footer>
</body>
</html>
