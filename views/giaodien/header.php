<?php
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
    <link rel="stylesheet" href="views/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="views/css/style.css">

</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <i class="fas fa-shipping-fast"></i>
                <span>LOGISMART</span>
            </div>
            <p>Hệ Thống Quản Lý Giao Hàng Thông Minh</p>
        </div>
    </header>
    
    <nav>
        <div class="container">
            <ul>
                <li><a href="index.php">Trang Chủ</a></li>
                <li><a href="?tracking_info">Tracking</a></li>
                <?php
                if(!isset($_SESSION['dangnhap']) || $_SESSION['dangnhap'] != 1){
                    echo '<li><a href="?login">Đăng Nhập</a></li>
                        <li><a href="?register">Đăng Ký</a></li>';
                }else{
                    // echo '<li><a href="?quanlydonhang">Quản Lý Đơn Hàng</a></li>
                    //     <li><a href="?quanlyuser">Quản Lý Nhân Viên</a></li>
                    //     <li><a href="?quanlykho">Quản Lý Kho</a></li>
                    //     <li><a href="?cod_dashboard">COD</a></li>
                    //     <li><a href="?dashboard">Báo Cáo & Thống Kê</a></li>';
                    // echo '<li><a href="?logout">Đăng Xuất</a></li>';

                    $role = $_SESSION['role'];
                    switch ($role) {
                        case 1:
                        case 2:
                            // Hiển thị tất cả
                            echo '<li><a href="?quanlydonhang">Quản Lý Đơn Hàng</a></li>
                                <li><a href="?quanlyuser">Quản Lý Nhân Viên</a></li>
                                <li><a href="?quanlyshipper">Shipper</a></li>
                                <li><a href="?dashboard">Báo Cáo & Thống Kê</a></li>';
                            break;
                            // <li><a href="?quanlykho">Quản Lý Kho</a></li>
                                // <li><a href="?cod_dashboard">COD</a></li>
                        case 3:
                        case 6:
                            // Chỉ quản lý đơn hàng
                            echo '<li><a href="?quanlydonhang">Quản Lý Đơn Hàng</a></li>';
                            break;
                        case 4:
                            // Chỉ quản lý kho
                            echo '<li><a href="?quanlykho">Quản Lý Kho</a></li>';
                            break;
                        case 5:
                            // Chỉ COD và báo cáo
                            echo '<li><a href="?cod_dashboard">COD</a></li>
                                <li><a href="?dashboard">Báo Cáo & Thống Kê</a></li>';
                            break;
                        default:
                            // Nếu role không xác định
                            echo '<li><a href="#">Vai trò không xác định</a></li>';
                            break;
                    }

                    echo '<li><a href="?logout">Đăng Xuất</a></li>';
                }
                
                ?>
                
            </ul>
        </div>
    </nav>
    <?php
        // var_dump($_SESSION["role"]);
        if (isset($_SESSION["dangnhap"]) && isset($_SESSION["user"])) {
            echo '<a href="/" class="btn-disabled welcome-message" style="text-decoration:none;">Xin chào ' . htmlspecialchars($_SESSION["user"]) . '</a>';
        }
    ?>              



</body>
