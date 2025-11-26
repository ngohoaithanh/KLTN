<style>

    .nav-user-info.nav-link {
    color: white !important;
    padding-top: 0;
    padding-bottom: 0;
    font-size: 16px;
    }
    .nav-user-info i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .nav-user-info:hover {
        background-color: rgba(255,255,255,0.1);
        border-radius: 4px;
    }

    /* Tùy chỉnh màu cho dropdown menu (vì nền nav là màu tối) */
    .dropdown-menu {
        background-color: var(--dark-color);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .dropdown-menu .dropdown-item {
        color: var(--dark-color);
    }
    .dropdown-menu .dropdown-item:hover {
        color: blue;
        background-color: rgba(255, 255, 255, 0.1);
    }
    .dropdown-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.15);
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
</style>
<?php
// FILE: views/giaodien/header.php (Đã nâng cấp Dropdown)
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
                <li><a href="index.php">Trang Chủ</a></li>
                
                <?php
                if(isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 1){
                    $role = $_SESSION['role'];
                    // Thêm các menu quản trị
                    if ($role == 1 || $role == 2) {
                        echo '<li><a href="?quanlydonhang">Quản Lý Đơn Hàng</a></li>';
                        echo '<li><a href="?quanlyuser">Quản Lý Nhân Viên</a></li>';
                        echo '<li><a href="?quanlyshipper">Shipper</a></li>';
                        echo '<li><a href="?dashboard">Báo Cáo & Thống Kê</a></li>';
                        echo '<li><a href="?cod_dashboard">COD</a></li>';
                    }elseif ($role == 5) {
                        echo '<li><a href="?dashboard">Báo Cáo & Thống Kê</a></li>';
                        echo '<li><a href="?cod_dashboard">COD</a></li>';
                    }
                    
                    $userAvatar = isset($_SESSION['avatar']) && !empty($_SESSION['avatar']) 
                                ? $_SESSION['avatar'] 
                                : 'views/img/avt.png';
                    
                    echo '
                    <li style="margin-left: auto;" class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-user-info" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            
                            <img src="' . htmlspecialchars($userAvatar) . '" alt="Avatar" 
                                style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 2px solid rgba(255,255,255,0.5);">
                            
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

                } else {
                    // Chưa đăng nhập (Bên phải)
                    echo '<li style="margin-left: auto;"><a href="?login">Đăng Nhập</a></li>';
                    // echo '<li><a href="?register">Đăng Ký</a></li>';
                }
                ?>
            </ul>
        </div>
    </nav>