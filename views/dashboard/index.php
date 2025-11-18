<?php
    if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
        echo "<script>alert('Bạn không có quyền truy cập!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
?>
<div class="container-fluid" style="margin-top: 20px;">
    <h1 class="h3 mb-0 text-gray-800 text-center">Dashboard Tổng Quan</h1>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 "></h1>
        <div class="d-flex align-items-center">
            <label for="date-range-filter" class="mb-0 mr-2">Xem theo:</label>
            <select id="date-range-filter" class="form-control mr-3" style="width: auto;">
                <option value="" disabled>Chọn khoảng thời gian</option>
                <option value="7" selected>7 ngày qua</option>
                <option value="30">30 ngày qua</option>
                <option value="90">90 ngày qua</option>
            </select>
            
            <label for="specific-date-filter" class="mb-0 mr-2">Hoặc chọn ngày:</label>
            <input type="date" id="specific-date-filter" class="form-control" style="width: auto;">
        </div>
    </div>
    <div class="row" id="kpi-cards-container">
        </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng quan Doanh thu & Đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area"><canvas id="dailyChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bổ Trạng thái</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4"><canvas id="statusPieChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân tích đơn hàng theo giờ</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 250px;"><canvas id="hourlyChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ tăng trưởng người dùng (Tích lũy)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area"><canvas id="growthChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Shipper hiệu quả (Theo số đơn đã giao)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="top-shippers-table"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Khách hàng thân thiết</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="top-customers-table"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bản đồ nhiệt các điểm giao thất bại</h6>
                </div>
                <div class="card-body p-0">
                    <div id="heatmap-map" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/maplibre-gl@2.4.0/dist/maplibre-gl.js'></script>
<link href='https://cdn.jsdelivr.net/npm/maplibre-gl@2.4.0/dist/maplibre-gl.css' rel='stylesheet' />

<script>
    let dailyChartInstance, pieChartInstance, hourlyChartInstance, growthChartInstance, heatmapMap;
    const GOONG_API_KEY = 'scmSgFcle8MbhKzOJMeUDIwuJWiwy6pOucLn1qQn'; // API Key của bạn

    // Lấy các phần tử lọc
    const dateRangeSelect = document.getElementById('date-range-filter');
    const specificDateInput = document.getElementById('specific-date-filter');

    // Tải dữ liệu lần đầu khi trang được tải xong
    document.addEventListener('DOMContentLoaded', () => {
        // Mặc định tải theo 7 ngày qua
        fetchDataAndRender(7, null);
    });

    // Xử lý khi chọn KHOẢNG NGÀY
    dateRangeSelect.addEventListener('change', (e) => {
        const days = e.target.value;
        if (days) {
            specificDateInput.value = ""; // Xóa ô chọn ngày để tránh xung đột
            fetchDataAndRender(days, null);
        }
    });

    // Xử lý khi chọn NGÀY CỤ THỂ
    specificDateInput.addEventListener('change', (e) => {
        const date = e.target.value;
        if (date) {
            dateRangeSelect.value = ""; // Reset ô chọn khoảng ngày
            fetchDataAndRender(null, date);
        }
    });

    // Hàm tải dữ liệu đã được nâng cấp
    async function fetchDataAndRender(days, date) {
        let apiUrl = 'api/dashboard/summary.php';

        // Xây dựng URL dựa trên bộ lọc nào đang hoạt động
        if (date) {
            apiUrl += `?date=${date}`;
        } else {
            apiUrl += `?days=${days || 7}`; // Mặc định là 7 nếu không có gì
        }
        
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            // Render tất cả các thành phần
            renderKpiCards(data.kpi);
            renderDailyChart(data.dailyChart);
            renderStatusPieChart(data.statusPieChart);
            renderTopShippers(data.topShippers);
            renderHourlyChart(data.hourlyStats);
            renderHeatmap(data.failedHeatmap);
            renderTopCustomers(data.topCustomers);
            renderGrowthChart(data.growthChart);

        } catch (error) {
            console.error("Lỗi khi tải dữ liệu dashboard:", error);
            alert("Đã xảy ra lỗi khi tải dữ liệu. Vui lòng kiểm tra Console (F12) để biết chi tiết.");
        }
    }

    // === CÁC HÀM RENDER (Giữ nguyên không đổi) ===
    
    function renderKpiCards(kpi) {
        // Phiên bản render 5 card của bạn
        document.getElementById('kpi-cards-container').innerHTML = `
            <div class="col-xl col-lg-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Đơn hàng (Nay)</div><div class="h5 mb-0 font-weight-bold text-gray-800">${kpi.total_orders_today || 0}</div></div><div class="col-auto"><i class="fas fa-calendar-day fa-2x text-gray-300"></i></div></div></div></div>
            </div>
            <div class="col-xl col-lg-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Doanh thu (Nay)</div><div class="h5 mb-0 font-weight-bold text-gray-800">${Number(kpi.total_revenue_today || 0).toLocaleString('vi-VN')}đ</div></div><div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div></div></div></div>
            </div>
            <div class="col-xl col-lg-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Shipper Online</div><div class="h5 mb-0 font-weight-bold text-gray-800">${kpi.active_shippers || 0}</div></div><div class="col-auto"><i class="fas fa-motorcycle fa-2x text-gray-300"></i></div></div></div></div>
            </div>
            <div class="col-xl col-lg-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đơn đang chờ</div><div class="h5 mb-0 font-weight-bold text-gray-800">${kpi.pending_orders || 0}</div></div><div class="col-auto"><i class="fas fa-hourglass-start fa-2x text-gray-300"></i></div></div></div></div>
            </div>
            <div class="col-xl col-lg-4 col-md-6 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Tổng Phí COD (Nay)</div><div class="h5 mb-0 font-weight-bold text-gray-800">${Number(kpi.total_cod_fee_today || 0).toLocaleString('vi-VN')}đ</div></div><div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i></div></div></div></div>
            </div>
        `;
    }

    function renderGrowthChart(data) {
        if (growthChartInstance) growthChartInstance.destroy();
        const ctx = document.getElementById('growthChart').getContext('2d');
        growthChartInstance = new Chart(ctx, {
            type: 'line',
            data: { labels: data.map(d => new Date(d.join_date).toLocaleDateString('vi-VN')), datasets: [ { label: 'Tổng Khách hàng', data: data.map(d => d.cumulative_customers), borderColor: 'rgba(78, 115, 223, 1)', backgroundColor: 'rgba(78, 115, 223, 0.1)', fill: true, tension: 0.3 }, { label: 'Tổng Shipper', data: data.map(d => d.cumulative_shippers), borderColor: 'rgba(28, 200, 138, 1)', backgroundColor: 'rgba(28, 200, 138, 0.1)', fill: true, tension: 0.3 } ] },
            options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
    }

    function renderDailyChart(data) {
        if (dailyChartInstance) dailyChartInstance.destroy();
        const ctx = document.getElementById('dailyChart').getContext('2d');
        dailyChartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels: data.map(d => d.date), datasets: [{ label: 'Doanh thu', data: data.map(d => d.total_revenue), backgroundColor: 'rgba(78, 115, 223, 0.8)', yAxisID: 'yRevenue' }, { label: 'Số đơn', data: data.map(d => d.total_orders), type: 'line', borderColor: 'rgba(28, 200, 138, 1)', yAxisID: 'yOrders' }] },
            options: { maintainAspectRatio: false, scales: { x: { type: 'time', time: { unit: 'day' } }, yRevenue: { type: 'linear', position: 'left', ticks: { callback: value => `${value/1000}k` } }, yOrders: { type: 'linear', position: 'right', grid: { drawOnChartArea: false } } } }
        });
    }
    
    function renderStatusPieChart(data) {
        if (pieChartInstance) pieChartInstance.destroy();
        const ctx = document.getElementById('statusPieChart').getContext('2d');
        pieChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: { labels: data.map(d => d.status), datasets: [{ data: data.map(d => d.count), backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e', '#36b9cc', '#858796', '#5a5c69'] }] },
            options: { maintainAspectRatio: false, cutout: '80%' }
        });
    }

    function renderTopShippers(data) {
        const table = document.getElementById('top-shippers-table');
        let html = '<thead class="thead-light"><tr><th>#</th><th>Tên Shipper</th><th>Số đơn đã giao</th></tr></thead><tbody>';
        if (data && data.length > 0) { data.forEach((shipper, index) => { html += `<tr><td>${index + 1}</td><td>${shipper.Username}</td><td><strong>${shipper.delivered_count}</strong></td></tr>`; }); } else { html += '<tr><td colspan="3" class="text-center">Chưa có dữ liệu.</td></tr>'; }
        html += '</tbody>';
        table.innerHTML = html;
    }
    
    function renderTopCustomers(data) {
        const table = document.getElementById('top-customers-table');
        let html = '<thead class="thead-light"><tr><th>#</th><th>Tên Khách hàng</th><th>Tổng số đơn</th></tr></thead><tbody>';
        if (data && data.length > 0) { data.forEach((customer, index) => { html += `<tr><td>${index + 1}</td><td>${customer.Username}</td><td><strong>${customer.order_count}</strong></td></tr>`; }); } else { html += '<tr><td colspan="3" class="text-center">Chưa có dữ liệu.</td></tr>'; }
        html += '</tbody>';
        table.innerHTML = html;
    }

    function renderHourlyChart(data) {
        if (hourlyChartInstance) hourlyChartInstance.destroy();
        const labels = Array.from({length: 24}, (_, i) => `${i}h`);
        const chartData = Array(24).fill(0);
        data.forEach(item => { chartData[item.hour] = item.order_count; });
        const ctx = document.getElementById('hourlyChart').getContext('2d');
        hourlyChartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels: labels, datasets: [{ label: 'Số đơn', data: chartData, backgroundColor: 'rgba(78, 115, 223, 0.8)' }] },
            options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
    }

    // function renderHeatmap(data) {
    //     if (!heatmapMap) {
    //         heatmapMap = new maplibregl.Map({
    //             container: 'heatmap-map',
    //             style: `https://tiles.goong.io/assets/goong_map_web.json?api_key=${GOONG_API_KEY}`,
    //             center: [106.7009, 10.7769],
    //             zoom: 11
    //         });
    //     }
    //     const geojsonData = { type: 'FeatureCollection', features: data.map(point => ({ type: 'Feature', geometry: { type: 'Point', coordinates: [point.lng, point.lat] } })) };
    //     heatmapMap.on('load', () => {
    //         const source = heatmapMap.getSource('failed-points');
    //         if (source) { source.setData(geojsonData); } else {
    //             heatmapMap.addSource('failed-points', { type: 'geojson', data: geojsonData });
    //             heatmapMap.addLayer({
    //                 id: 'heatmap-layer', type: 'heatmap', source: 'failed-points',
    //                 paint: { 'heatmap-intensity': 2, 'heatmap-color': [ 'interpolate', ['linear'], ['heatmap-density'], 0, 'rgba(33,102,172,0)', 0.2, 'rgb(103,169,207)', 0.4, 'rgb(209,229,240)', 0.6, 'rgb(253,219,199)', 0.8, 'rgb(239,138,98)', 1, 'rgb(178,24,43)' ], 'heatmap-radius': 50, 'heatmap-opacity': 0.7 }
    //             });
    //         }
    //     });
    // }
    function renderHeatmap(data) {
        if (!heatmapMap) {
            heatmapMap = new maplibregl.Map({
                container: 'heatmap-map',
                style: `https://tiles.goong.io/assets/goong_map_web.json?api_key=${GOONG_API_KEY}`,
                center: [106.7009, 10.7769],
                zoom: 11
            });
        }
        
        const geojsonData = { type: 'FeatureCollection', features: data.map(point => ({ type: 'Feature', geometry: { type: 'Point', coordinates: [point.lng, point.lat] } })) };

        heatmapMap.on('load', () => {
            const source = heatmapMap.getSource('failed-points');
            if (source) { 
                source.setData(geojsonData); 
            } else {
                heatmapMap.addSource('failed-points', { type: 'geojson', data: geojsonData });
                heatmapMap.addLayer({
                    id: 'heatmap-layer', 
                    type: 'heatmap', 
                    source: 'failed-points',
                    paint: {
                        // === PHẦN NÂNG CẤP ĐỂ HIỂN THỊ RÕ HƠN ===

                        // 1. Tăng cường độ tổng thể của vầng nhiệt
                        'heatmap-intensity': 2.5,
                        
                        // 2. Thay đổi dải màu để "nóng" ngay lập tức
                        'heatmap-color': [
                            'interpolate', ['linear'], ['heatmap-density'],
                            0, 'rgba(231, 74, 59, 0)',    // 0% mật độ = trong suốt
                            0.1, 'rgba(231, 74, 59, 0.6)', // 10% mật độ = đỏ mờ (quan trọng)
                            0.5, 'rgba(231, 74, 59, 0.9)', // 50% mật độ = đỏ đậm
                            1, 'rgb(245, 150, 140)'       // 100% mật độ = đỏ sáng
                        ],
                        
                        // 3. Tăng bán kính ảnh hưởng của mỗi điểm
                        'heatmap-radius': 80, // Tăng mạnh từ 50 lên 80

                        // 4. Tăng độ mờ đục tổng thể
                        'heatmap-opacity': 0.9 
                    }
                });
            }
        });
    }
</script>