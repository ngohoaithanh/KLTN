<?php
// FILE: views/giaodien/header2.php (Đã thêm link Profile)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>LOGISMART - Quản trị</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="views/css/style2.css">
</head>
<body id="page-top">

<div id="wrapper">

    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index2.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <div class="sidebar-brand-text mx-3">LOGISMART</div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item active">
            <a class="nav-link" href="index2.php">
                <i class="fas fa-fw fa-home"></i>
                <span>Trang Chủ</span></a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Quản lý
        </div>
        
        <?php if(isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 1): ?>
            <?php
                $role = $_SESSION['role'];
                // Hiển thị menu theo vai trò
                if ($role == 1 || $role == 2) { // Admin & Quản lý
                    echo '<li class="nav-item"><a class="nav-link" href="?dashboard"><i class="fas fa-fw fa-chart-pie"></i><span>Dashboard</span></a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="?quanlydonhang"><i class="fas fa-fw fa-box-open"></i><span>Đơn hàng</span></a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="?quanlyshipper"><i class="fas fa-fw fa-motorcycle"></i><span>Shipper</span></a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="?quanlyuser"><i class="fas fa-fw fa-users-cog"></i><span>User</span></a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="?cod_dashboard"><i class="fas fa-fw fa-file-invoice-dollar"></i><span>Đối soát COD</span></a></li>';
                } elseif ($role == 6 || $role == 3) { // Shipper & Nhân viên tiếp nhận
                    echo '<li class="nav-item"><a class="nav-link" href="?quanlydonhang"><i class="fas fa-fw fa-box-open"></i><span>Đơn hàng</span></a></li>';
                } elseif ($role == 4) { // Quản lý kho
                    echo '<li class="nav-item"><a class="nav-link" href="?quanlykho"><i class="fas fa-fw fa-warehouse"></i><span>Quản lý Kho</span></a></li>';
                } elseif ($role == 5) { // Kế toán
                    echo '<li class="nav-item"><a class="nav-link" href="?cod_dashboard"><i class="fas fa-fw fa-file-invoice-dollar"></i><span>Đối soát COD</span></a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="?dashboard"><i class="fas fa-fw fa-chart-pie"></i><span>Báo Cáo</span></a></li>';
                }
            ?>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="?login">
                    <i class="fas fa-fw fa-sign-in-alt"></i>
                    <span>Đăng Nhập</span></a>
            </li>
        <?php endif; ?>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>

                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($_SESSION["user"] ?? 'Guest') ?></span>
                            <img class="img-profile rounded-circle" src="views/img/avt.png"> 
                        </a>
                        <?php if(isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 1): ?>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            
                            <a class="dropdown-item" href="?profile">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Hồ sơ cá nhân
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="?logout" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Đăng xuất
                            </a>
                        </div>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
            <div class="container-fluid">