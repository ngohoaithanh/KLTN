<?php
// FILE: views/giaodien/intro_dashboard.php
// (Đã thiết kế lại để dùng CSS của file style.css cũ)

// Lấy thông tin vai trò (nếu đã đăng nhập)
$user_role = $_SESSION['role'] ?? null;
?>

<style>
    .kpi-card-intro {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .kpi-card-intro .kpi-title {
        font-size: 15px;
        font-weight: 600;
        color: #777;
        text-transform: uppercase;
    }
    .kpi-card-intro .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark-color); /* Lấy màu từ file style.css của bạn */
    }
    .kpi-card-intro .kpi-icon i {
        font-size: 36px;
        color: var(--primary-color);
        opacity: 0.3;
    }
    .quick-links-widget {
        background: #fff; 
        padding: 20px; 
        border-radius: 8px; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .quick-links-widget .btn {
        margin-bottom: 10px;
        margin-right: 10px;
        width: 260px; 
        text-align: left;
    }
    .quick-links-widget .btn i { 
        margin-right: 15px; 
        width: 20px; 
        text-align: center;
    }
</style>
<div class="container-fluid" id="staff" style="margin-top: 20px;">
<h2 style="font-weight: 600; color: var(--dark-color); margin-bottom: 20px;">Bảng điều khiển nhanh</h2>

<div class="dashboard">

    <div class="kpi-card-intro">
        <div>
            <div class="kpi-title" style="color: var(--warning-color);">Đơn hàng đang chờ</div>
            <div class="kpi-value" id="intro1_kpi_pending">...</div>
        </div>
        <div class="kpi-icon"><i class="fas fa-hourglass-start" style="color: var(--warning-color);"></i></div>
    </div>

    <div class="kpi-card-intro">
        <div>
            <div class="kpi-title" style="color: #17a2b8;">Shipper Online</div>
            <div class="kpi-value" id="intro1_kpi_online">...</div>
        </div>
        <div class="kpi-icon"><i class="fas fa-motorcycle" style="color: #17a2b8;"></i></div>
    </div>

    <div class="kpi-card-intro">
        <div>
            <div class="kpi-title" style="color: var(--primary-color);">Đơn hàng (Hôm nay)</div>
            <div class="kpi-value" id="intro1_kpi_orders_today">...</div>
        </div>
        <div class="kpi-icon"><i class="fas fa-calendar-day" style="color: var(--primary-color);"></i></div>
    </div>

    <div class="kpi-card-intro">
        <div>
            <div class="kpi-title" style="color: var(--secondary-color);">Doanh thu (Hôm nay)</div>
            <div class="kpi-value" id="intro1_kpi_revenue_today">...</div>
        </div>
        <div class="kpi-icon"><i class="fas fa-dollar-sign" style="color: var(--secondary-color);"></i></div>
    </div>
</div>

<div class="row">

    <div class="col-md-6">
        <div class="quick-links-widget">
            <h3 class="card-header" style="border: none; padding-left: 0;">Lối tắt nhanh</h3>
            <div class="quick-links">
                
                <?php
                // Logic hiển thị lối tắt dựa trên vai trò
                switch ($user_role):
                    case 1:
                    case 2: // Admin / Quản lý
                ?>
                    <a href="?dashboard" class="btn btn-primary"><i class="fas fa-chart-pie fa-fw"></i>Xem Dashboard Tổng quan</a>
                    <a href="?cod_dashboard" class="btn btn-danger"><i class="fas fa-file-invoice-dollar fa-fw"></i>Đối Soát Công Nợ</a>
                    <a href="?quanlyshipper" class="btn" style="background-color: #17a2b8; color:white;"><i class="fas fa-motorcycle fa-fw"></i>Quản lý Shipper</a>
                <?php
                        break;
                    case 5: // Kế toán
                ?>
                    <a href="?cod_dashboard" class="btn btn-danger"><i class="fas fa-file-invoice-dollar fa-fw"></i>Trung tâm Đối soát COD</a>
                    <a href="?dashboard" class="btn btn-primary"><i class="fas fa-chart-pie fa-fw"></i>Xem Báo cáo & Thống kê</a>
                <?php
                        break;
                    default: // Các vai trò khác
                ?>
                     <a href="?quanlydonhang" class="btn btn-primary"><i class="fas fa-box-open fa-fw"></i>Quản lý Đơn hàng</a>
                <?php
                        break;
                endswitch;
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="quick-links-widget">
            <h3 class="card-header" style="border: none; padding-left: 0;">Chào mừng trở lại!</h3>
            <p>Bạn có thể sử dụng menu bên trên để truy cập tất cả các chức năng. Các chỉ số hiệu suất chính được hiển thị ở đây để giúp bạn nắm bắt nhanh tình hình.</p>
            <p class="mb-0">Chúc bạn một ngày làm việc hiệu quả!</p>
        </div>
    </div>
</div>
</div>

<script>
    // Hàm định dạng tiền tệ
    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(parseFloat(number || 0));
    }

    // Tự động chạy khi trang được tải
    document.addEventListener('DOMContentLoaded', async function() {
        try {
            // Tái sử dụng API của dashboard tổng
            const response = await fetch('api/dashboard/summary.php');
            const data = await response.json();

            if (data.kpi) {
                // Cập nhật các thẻ KPI
                document.getElementById('intro1_kpi_pending').textContent = data.kpi.pending_orders || 0;
                document.getElementById('intro1_kpi_online').textContent = data.kpi.active_shippers || 0;
                document.getElementById('intro1_kpi_orders_today').textContent = data.kpi.total_orders_today || 0;
                document.getElementById('intro1_kpi_revenue_today').textContent = formatCurrency(data.kpi.total_revenue_today);
            }
        } catch (error) {
            console.error("Lỗi khi tải dữ liệu KPI cho trang chủ:", error);
        }
    });
</script>