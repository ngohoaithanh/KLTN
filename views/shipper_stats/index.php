<?php
// Lấy ID của shipper từ URL
$shipperId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($shipperId == 0) {
    echo "<h1>ID Shipper không hợp lệ!</h1>";
    exit();
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê chi tiết Shipper: <span id="shipper-name-title">Đang tải...</span></h1>
        <div>
            <label for="date-range-filter">Xem theo:</label>
            <select id="date-range-filter" class="form-control" style="display: inline-block; width: auto;">
                <option value="7" selected>7 ngày qua</option>
                <option value="30">30 ngày qua</option>
                <option value="90">90 ngày qua</option>
            </select>
        </div>
    </div>

    <div class="row" id="kpi-cards-container">
        </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Số lượng đơn hàng theo ngày</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailyOrdersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ trạng thái đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const shipperId = <?php echo $shipperId; ?>;
    let dailyChartInstance;
    let pieChartInstance;

    function formatSeconds(seconds) {
    if (!seconds || seconds <= 0) {
        return 'N/A';
    }
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.round(seconds % 60);
    return `${minutes} phút ${remainingSeconds} giây`;
}

    async function fetchDataAndRender(days = 7) {
        try {
            const response = await fetch(`api/shipper/getShipperDetailStats.php?id=${shipperId}&days=${days}`);
            const data = await response.json();

            // Cập nhật tên shipper
            document.getElementById('shipper-name-title').textContent = data.shipperInfo.Username;

            // Render các thẻ KPI
            renderKpiCards(data.kpiStats);

            // Render biểu đồ
            renderDailyOrdersChart(data.dailyOrdersChart);
            renderStatusPieChart(data.statusPieChart);

        } catch (error) {
            console.error("Lỗi khi tải dữ liệu thống kê:", error);
        }
    }
    
    // code thời gian giao hàng trung bình
//     function renderKpiCards(kpi) {
//     const successRate = (kpi.total_orders > 0) ? ((kpi.delivered_orders / kpi.total_orders) * 100).toFixed(1) : 0;
    
//     // Sử dụng hàm formatSeconds mới
//     const avgDeliveryTime = formatSeconds(kpi.avg_delivery_time_seconds);

//     const kpiContainer = document.getElementById('kpi-cards-container');
//     kpiContainer.innerHTML = `
//         <div class="col-xl-3 col-md-6 mb-4">
//             <div class="card border-left-primary shadow h-100 py-2">
//                 <div class="card-body">
//                     <div class="row no-gutters align-items-center">
//                         <div class="col mr-2">
//                             <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đơn đã nhận</div>
//                             <div class="h5 mb-0 font-weight-bold text-gray-800">${kpi.total_orders || 0}</div>
//                         </div>
//                         <div class="col-auto">
//                             <i class="fas fa-receipt fa-2x text-gray-300"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
//         <div class="col-xl-3 col-md-6 mb-4">
//             <div class="card border-left-success shadow h-100 py-2">
//                 <div class="card-body">
//                      <div class="row no-gutters align-items-center">
//                         <div class="col mr-2">
//                             <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tỷ lệ thành công</div>
//                             <div class="h5 mb-0 font-weight-bold text-gray-800">${successRate}%</div>
//                         </div>
//                         <div class="col-auto">
//                             <i class="fas fa-check-circle fa-2x text-gray-300"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
//         <div class="col-xl-3 col-md-6 mb-4">
//             <div class="card border-left-info shadow h-100 py-2">
//                 <div class="card-body">
//                      <div class="row no-gutters align-items-center">
//                         <div class="col mr-2">
//                             <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Doanh thu phí VC</div>
//                             <div class="h5 mb-0 font-weight-bold text-gray-800">${Number(kpi.total_fee || 0).toLocaleString('vi-VN')}đ</div>
//                         </div>
//                         <div class="col-auto">
//                             <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
//          <div class="col-xl-3 col-md-6 mb-4">
//             <div class="card border-left-warning shadow h-100 py-2">
//                 <div class="card-body">
//                      <div class="row no-gutters align-items-center">
//                         <div class="col mr-2">
//                             <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Thời gian giao TB</div>
//                             <div class="h5 mb-0 font-weight-bold text-gray-800">${avgDeliveryTime}</div>
//                         </div>
//                         <div class="col-auto">
//                             <i class="fas fa-clock fa-2x text-gray-300"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
//     `;
// }
function renderKpiCards(kpi) {
    const successRate = (kpi.total_orders > 0) ? ((kpi.delivered_orders / kpi.total_orders) * 100).toFixed(1) : 0;
    const kpiContainer = document.getElementById('kpi-cards-container');

    kpiContainer.innerHTML = `
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đơn đã nhận</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${kpi.total_orders || 0}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tỷ lệ thành công</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${successRate}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Doanh thu phí VC</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${Number(kpi.total_fee || 0).toLocaleString('vi-VN')}đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Số đơn thất bại</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${kpi.failed_orders || 0}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

    function renderDailyOrdersChart(data) {
        if (dailyChartInstance) {
            dailyChartInstance.destroy();
        }
        const ctx = document.getElementById('dailyOrdersChart').getContext('2d');
        dailyChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => new Date(item.order_date).toLocaleDateString('vi-VN')),
                datasets: [{
                    label: 'Số đơn',
                    data: data.map(item => item.order_count),
                    borderColor: 'rgba(78, 115, 223, 1)',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    fill: true,
                }]
            },
            options: { maintainAspectRatio: false }
        });
    }
    
    function renderStatusPieChart(data) {
        if(pieChartInstance) {
            pieChartInstance.destroy();
        }
        const ctx = document.getElementById('statusPieChart').getContext('2d');
        pieChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(item => {
                    if(item.status == 'delivered') return 'Thành công';
                    if(item.status == 'delivery_failed') return 'Thất bại';
                    return 'Đã hủy';
                }),
                datasets: [{
                    data: data.map(item => item.count),
                    backgroundColor: ['#1cc88a', '#e74a3b', '#858796'],
                }]
            },
            options: { maintainAspectRatio: false, cutout: '80%' }
        });
    }

    // Tải dữ liệu lần đầu
    document.addEventListener('DOMContentLoaded', () => {
        fetchDataAndRender(7);
    });

    // Gắn sự kiện cho bộ lọc
    document.getElementById('date-range-filter').addEventListener('change', (e) => {
        const selectedDays = e.target.value;
        fetchDataAndRender(selectedDays);
    });
</script>