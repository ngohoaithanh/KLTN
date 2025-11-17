<?php
// FILE: views/giaodien/intro.php (Nâng cấp)

// Lấy thông tin vai trò và tên của người dùng từ Session
$user_role = $_SESSION['role'] ?? null;
$user_name = $_SESSION['user'] ?? 'Khách';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chào mừng trở lại, <?= htmlspecialchars($user_name) ?>!</h1>
</div>

<div class="row" id="kpi-cards-container">
    <?php
    // Hiển thị KPI khác nhau cho các vai trò khác nhau
    // Admin, Quản lý (1, 2) và Kế toán (5) sẽ thấy các KPI tổng quan của hệ thống.
    if ($user_role == 1 || $user_role == 2 || $user_role == 5):
    ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đơn hàng đang chờ</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="intro_kpi_pending">...</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hourglass-start fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Shipper Online</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="intro_kpi_online">...</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-motorcycle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Doanh thu (Hôm nay)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="intro_kpi_revenue_today">...</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Tổng Phí COD (Nay)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="intro_kpi_cod_fee">...</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row">

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lối tắt nhanh</h6>
            </div>
            <div class="card-body">
                <?php
                // Logic hiển thị lối tắt dựa trên vai trò
                switch ($user_role):

                    // --- Trường hợp 1: Admin / Quản lý (Vai trò 1, 2) ---
                    case 1:
                    case 2:
                ?>
                        <p>Các chức năng quản trị và điều hành hệ thống quan trọng nhất.</p>
                        <a href="?dashboard" class="btn btn-primary btn-icon-split mb-2">
                            <span class="icon text-white-50"><i class="fas fa-chart-pie"></i></span>
                            <span class="text">Xem Dashboard Tổng quan</span>
                        </a>
                        <a href="?cod_dashboard" class="btn btn-danger btn-icon-split mb-2">
                            <span class="icon text-white-50"><i class="fas fa-file-invoice-dollar"></i></span>
                            <span class="text">Đối Soát Công Nợ</span>
                        </a>
                        <a href="?quanlyshipper" class="btn btn-info btn-icon-split mb-2">
                            <span class="icon text-white-50"><i class="fas fa-motorcycle"></i></span>
                            <span class="text">Quản lý Shipper</span>
                        </a>
                        <a href="?quanlyuser" class="btn btn-secondary btn-icon-split mb-2">
                            <span class="icon text-white-50"><i class="fas fa-users-cog"></i></span>
                            <span class="text">Quản lý User</span>
                        </a>
                <?php
                        break;

                    // --- Trường hợp 2: Kế toán (Vai trò 5) ---
                    case 5:
                ?>
                        <p>Các chức năng tài chính và đối soát quan trọng nhất.</p>
                        <a href="?cod_dashboard" class="btn btn-danger btn-icon-split mb-2">
                            <span class="icon text-white-50"><i class="fas fa-file-invoice-dollar"></i></span>
                            <span class="text">Trung tâm Đối soát COD</span>
                        </a>
                        <a href="?dashboard" class="btn btn-primary btn-icon-split mb-2">
                            <span class="icon text-white-50"><i class="fas fa-chart-pie"></i></span>
                            <span class="text">Xem Báo cáo & Thống kê</span>
                        </a>
                <?php
                        break;

                    // --- Trường hợp 3: Shipper (Vai trò 6) ---
                    case 6:
                ?>
                        <p>Các tác vụ quan trọng dành cho bạn.</p>
                        <a href="?quanlydonhang" class="btn btn-primary btn-icon-split mb-2">
                            <span class="icon text-white-50"><i class="fas fa-box-open"></i></span>
                            <span class="text">Xem Đơn hàng của tôi</span>
                        </a>
                        <?php
                        break;

                    // --- Trường hợp 4: Các vai trò khác (Nhân viên, QL Kho...) ---
                    default:
                ?>
                        
                        <?php if ($user_role == 4): ?>
                            <a href="?quanlykho" class="btn btn-info btn-icon-split mb-2">
                                <span class="icon text-white-50"><i class="fas fa-warehouse"></i></span>
                                <span class="text">Quản lý Kho</span>
                            </a>
                        <?php endif; ?>
                <?php
                        break;
                endswitch;
                ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông báo</h6>
            </div>
            <div class="card-body">
                <p>Chào mừng bạn đến với hệ thống quản lý giao hàng LOGISMART phiên bản mới.</p>
                <p>Bạn có thể sử dụng menu bên trái để truy cập tất cả các chức năng được phân quyền. Các chỉ số hiệu suất chính được hiển thị ở trên để giúp bạn nắm bắt nhanh tình hình hệ thống.</p>
                <p class="mb-0">Chúc bạn một ngày làm việc hiệu quả!</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm định dạng tiền tệ
    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
    }

    // Tự động chạy khi trang được tải
    document.addEventListener('DOMContentLoaded', async function() {
        try {
            // Tái sử dụng API của dashboard tổng
            const response = await fetch('api/dashboard/summary.php');
            const data = await response.json();

            if (data.kpi) {
                // Kiểm tra sự tồn tại của các element trước khi gán
                // (Vì các vai trò khác nhau sẽ có các card khác nhau)
                
                const kpiPending = document.getElementById('intro_kpi_pending');
                if (kpiPending) kpiPending.textContent = data.kpi.pending_orders || 0;
                
                const kpiOnline = document.getElementById('intro_kpi_online');
                if (kpiOnline) kpiOnline.textContent = data.kpi.active_shippers || 0;
                
                const kpiOrders = document.getElementById('intro_kpi_orders_today');
                if (kpiOrders) kpiOrders.textContent = data.kpi.total_orders_today || 0;
                
                const kpiRevenue = document.getElementById('intro_kpi_revenue_today');
                if (kpiRevenue) kpiRevenue.textContent = formatCurrency(data.kpi.total_revenue_today || 0);

                const kpiCodFee = document.getElementById('intro_kpi_cod_fee');
                if (kpiCodFee) kpiCodFee.textContent = formatCurrency(data.kpi.total_cod_fee_today || 0);
            }
        } catch (error) {
            console.error("Lỗi khi tải dữ liệu KPI cho trang chủ:", error);
        }
    });
</script>