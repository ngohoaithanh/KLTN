<style>
    /* ====== STYLE CHO NAV TÀI KHOẢN (USER DROPDOWN) ====== */
    .nav-user-info.nav-link {
        color: white !important;
        padding-top: 0;
        padding-bottom: 0;
        font-size: 16px;
        display: flex;
        align-items: center;
    }
    .nav-user-info i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .nav-user-info:hover {
        background-color: rgba(255,255,255,0.1);
        border-radius: 4px;
    }

    /* ====== STYLE CHUNG CHO DROPDOWN MENU (TRÊN NỀN TỐI) ====== */
    .dropdown-menu {
        background-color: var(--dark-color);
        border: 1px solid rgba(255, 255, 255, 0.15);
        min-width: 220px;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }
    .dropdown-menu .dropdown-item {
        color: black;
        font-size: 14px;
        padding: 0.35rem 1rem;
    }
    .dropdown-menu .dropdown-item i {
        width: 18px;
        text-align: center;
        margin-right: 6px;
    }
    .dropdown-menu .dropdown-item:hover {
        color: blue;
        background-color: rgba(255, 255, 255, 0.08);
    }
    .dropdown-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        margin: 0.25rem 0;
    }

    /* Nút đăng xuất bên trong dropdown */
    .nav-logout-btn-dropdown {
        color: var(--danger-color) !important;
        font-weight: bold;
    }
    .nav-logout-btn-dropdown:hover {
        color: white !important;
        background-color: var(--danger-color) !important;
    }

    /* ====== CĂN CHỈNH NAVBAR CHUNG ====== */
    nav ul {
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }
    nav ul li {
        list-style: none;
        margin-right: 10px;
        position: relative;
    }
    nav ul li a {
        color: #ffffff;
        text-decoration: none;
        padding: 8px 12px;
        display: block;
    }
    nav ul li a:hover {
        background-color: rgba(255,255,255,0.1);
        border-radius: 4px;
    }

    /* Đảm bảo dropdown Bootstrap hoạt động trong nav custom */
    .nav-item.dropdown > a.nav-link {
        padding: 8px 12px;
    }
    
    /* Giảm khoảng cách giữa avatar và chuông */
    nav ul li.nav-item.dropdown.no-arrow.mx-1 {
        margin-left: 4px;   /* hoặc 0 nếu muốn dính hẳn */
        margin-right: 0;
    }

    /* Nếu muốn avatar không có margin-right quá lớn */
    nav ul li.nav-item.dropdown.nav-user-info-li {
        margin-right: 4px;
    }
    #alertsDropdown::after {
        display: none !important;
    }
    /* 
    #alertsDropdown {
        padding: 0 8px !important;
    }

    #alertsDropdown i {
        font-size: 18px;
    } */
    @keyframes bell-ring {
        0% { transform: rotate(0); }
        15% { transform: rotate(15deg); }
        30% { transform: rotate(-15deg); }
        45% { transform: rotate(10deg); }
        60% { transform: rotate(-10deg); }
        75% { transform: rotate(5deg); }
        100% { transform: rotate(0); }
    }

    /* Class rung */
    .bell-shake {
        animation: bell-ring 0.6s ease;
    }
    /* .nav-item.dropdown:hover > .dropdown-menu {
        display: block;
    } */
</style>

<?php
// FILE: views/giaodien/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Lý Giao Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="views/css/style.css">
</head>
<body>
    <header>
        <div class="container-fluid"> 
            <div class="logo">
                <i class="fas fa-shipping-fast"></i>
                <span>LOGISMART</span>
            </div>
            <p>Hệ Thống Quản Lý Giao Hàng Thông Minh</p>
        </div>
    </header>
    
    <nav>
        <div class="container-fluid"> 
            <ul>
                <!-- TRANG CHỦ LUÔN CÓ -->
                <li><a href="index.php">Trang chủ</a></li>
                
                <?php
                if (isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 1) {
                    $role = $_SESSION['role'];

                    // ====== ADMIN / MANAGER (role 1,2) ======
                    if ($role == 1 || $role == 2) {

                        // DASHBOARD RIÊNG
                        echo '<li><a href="?dashboard">Dashboard</a></li>';

                        // NHÓM VẬN HÀNH
                        echo '
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="opsMenu" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Vận hành
                            </a>
                            <div class="dropdown-menu" aria-labelledby="opsMenu">
                                <a class="dropdown-item" href="?quanlydonhang">
                                    <i class="fas fa-file-invoice"></i> Đơn hàng
                                </a>
                                <a class="dropdown-item" href="?quanlyshipper">
                                    <i class="fas fa-motorcycle"></i> Shipper
                                </a>
                                <a class="dropdown-item" href="?incident_reports">
                                    <i class="fas fa-exclamation-triangle"></i> Sự cố & Khiếu nại
                                </a>
                                <a class="dropdown-item" href="?pricing">
                                    <i class="fas fa-tags"></i> Giá cước
                                </a>
                            </div>
                        </li>';

                        // NHÓM NHÂN SỰ
                        echo '
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="hrMenu" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Nhân sự
                            </a>
                            <div class="dropdown-menu" aria-labelledby="hrMenu">
                                <a class="dropdown-item" href="?quanlyuser">
                                    <i class="fas fa-users-cog"></i> Nhân viên
                                </a>
                                <a class="dropdown-item" href="?quanlyshipper">
                                    <i class="fas fa-user-shield"></i> Shipper
                                </a>
                            </div>
                        </li>';

                        // NHÓM TÀI CHÍNH
                        echo '
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="financeMenu" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </i>Tài chính
                            </a>
                            <div class="dropdown-menu" aria-labelledby="financeMenu">
                                <a class="dropdown-item" href="?cod_dashboard">
                                    <i class="fas fa-money-check-alt"></i> COD
                                </a>
                                <a class="dropdown-item" href="?dashboard">
                                    <i class="fas fa-chart-line"></i> Báo cáo & Thống kê
                                </a>';
                                // Nhật ký hoạt động chỉ cho admin
                                if ($role == 1) {
                                    echo '
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="?system_logs">
                                        <i class="fas fa-clipboard-list"></i> Nhật ký HĐ
                                    </a>';
                                }
                        echo '
                            </div>
                        </li>';

                        // THÔNG BÁO RIÊNG
                        echo '
                        <li>
                            <a href="?send_notification">
                                Thông báo
                            </a>
                        </li>';

                    // ====== VÍ DỤ ROLE 5: CHỈ TÀI CHÍNH / BÁO CÁO ======
                    } elseif ($role == 5) {

                        echo '
                        <li><a href="?dashboard"><i class="fas fa-tachometer-alt mr-1"></i>Dashboard</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="financeMenu" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-coins mr-1"></i>Tài chính
                            </a>
                            <div class="dropdown-menu" aria-labelledby="financeMenu">
                                <a class="dropdown-item" href="?dashboard">
                                    <i class="fas fa-chart-line"></i> Báo cáo & Thống kê
                                </a>
                                <a class="dropdown-item" href="?cod_dashboard">
                                    <i class="fas fa-money-check-alt"></i> COD
                                </a>
                            </div>
                        </li>';
                    }

                    // AVATAR & USER DROPDOWN BÊN PHẢI
                    $userAvatar = isset($_SESSION['avatar']) && !empty($_SESSION['avatar']) 
                                ? $_SESSION['avatar'] 
                                : 'views/img/avt.png';
                    
                    

                    echo '
                    <li style="margin-left: auto;" class="nav-item dropdown nav-user-info-li">
                        <a class="nav-link dropdown-toggle nav-user-info" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            
                            <img src="' . htmlspecialchars($userAvatar) . '" alt="Avatar" 
                                style="width: 25px; height: 25px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 2px solid rgba(255,255,255,0.5);">
                            
                            <strong>' . htmlspecialchars($_SESSION["user"]) . '</strong>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="?profile">
                                <i class="fas fa-user-cog fa-fw mr-2"></i>Hồ sơ cá nhân
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item nav-logout-btn-dropdown" href="?logout">
                                <i class="fas fa-sign-out-alt fa-fw mr-2"></i>Đăng xuất
                            </a>
                        </div>
                    </li>';
                    echo '
                    <li class="nav-item dropdown no-arrow mx-1" >
                        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="position: relative; padding: 0 15px !important; height: 100%; display: flex; align-items: center;">
                            <i class="fas fa-bell fa-fw" style="color: white;"></i>
                            <span class="badge badge-pill badge-danger" id="notif-count" style="position: absolute; top: 10px; right: 8px;"></span>
                        </a>
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">Trung tâm thông báo</h6>
                            <div id="notif-list" style="max-height: 300px; overflow-y: auto;">
                                <a class="dropdown-item text-center small text-gray-500" href="#">Đang tải...</a>
                            </div>
                        </div>
                    </li>';

                } else {
                    // Chưa đăng nhập (Bên phải)
                    echo '<li style="margin-left: auto;"><a href="?login">Đăng nhập</a></li>';
                    // echo '<li><a href="?register">Đăng ký</a></li>';
                }
                ?>
            </ul>
        </div>
    </nav>
