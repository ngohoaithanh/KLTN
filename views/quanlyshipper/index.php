<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Tổng quan Quản lý Shipper</h1>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bản đồ Shipper đang hoạt động</h6>
                </div>
                <div class="card-body p-0">
                    <div id="shipper-map" style="height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách Shipper</h6>
                    <div class="d-flex">
                        <select id="filter-status" class="form-control mr-2" style="width: 150px;">
                            <option value="all">Tất cả trạng thái</option>
                            <option value="online">Online</option>
                            <option value="busy">Busy</option>
                            <option value="offline">Offline</option>
                        </select>
                        <input type="text" id="search-shipper" class="form-control" placeholder="Tìm theo tên/SĐT..." style="width: 250px;">
                    </div>
                    <a href="index.php?addUser&role=6" class="btn btn-success">
                        <i class="fas fa-plus mr-2"></i>Thêm Shipper
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="shipper-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên Shipper</th>
                                    <th>Số điện thoại</th>
                                    <th>Trạng thái</th>
                                    <th>Đánh giá</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="shipper-table-body">
                                </tbody>
                        </table>
                        <div id="loading-spinner" class="text-center p-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="shipperDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary">
                    <i class="fas fa-user-circle mr-2"></i>Chi tiết Shipper: <span id="detail-name-title"></span>
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-envelope fa-fw mr-3 text-gray-500"></i>Email</span>
                        <strong id="detail-email"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-phone fa-fw mr-3 text-gray-500"></i>Số điện thoại</span>
                        <strong id="detail-phone"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-toggle-on fa-fw mr-3 text-gray-500"></i>Trạng thái</span>
                        <span id="detail-status"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-id-card fa-fw mr-3 text-gray-500"></i>Biển số xe</span>
                        <strong id="detail-license"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-motorcycle fa-fw mr-3 text-gray-500"></i>Loại xe</span>
                        <strong id="detail-vehicle"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-star fa-fw mr-3 text-gray-500"></i>Đánh giá</span>
                        <strong id="detail-rating"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock fa-fw mr-3 text-gray-500"></i>Vị trí cập nhật</span>
                        <em id="detail-updated"></em>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Đóng
                </button>
            </div>
        </div>
    </div>
</div>
<script src='https://cdn.jsdelivr.net/npm/maplibre-gl@2.4.0/dist/maplibre-gl.js'></script>
<link href='https://cdn.jsdelivr.net/npm/maplibre-gl@2.4.0/dist/maplibre-gl.css' rel='stylesheet' />

<script>
    // === KHAI BÁO BIẾN TOÀN CỤC ===
    let map;
    let allShippers = []; // Lưu trữ toàn bộ danh sách shipper
    let shipperMarkers = {}; // Lưu marker trên bản đồ
    const API_URL = 'api/shipper/getAllShippers.php';
    
    // !!! QUAN TRỌNG: Thay API Key của bạn vào đây
    const GOONG_API_KEY = 'scmSgFcle8MbhKzOJMeUDIwuJWiwy6pOucLn1qQn';

    // === KHỞI TẠO ===
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        loadAllShippers();
        setupEventListeners();
        // Tự động cập nhật vị trí marker mỗi 20 giây
        setInterval(updateActiveShipperMarkers, 20000);
    });

    // === CÁC HÀM XỬ LÝ ===

    /** Khởi tạo bản đồ Goong Maps */
    function initMap() {
        map = new maplibregl.Map({
            container: 'shipper-map',
            style: `https://tiles.goong.io/assets/goong_map_web.json?api_key=${GOONG_API_KEY}`,
            center: [106.7009, 10.7769], // [Kinh độ, Vĩ độ] - Trung tâm TP.HCM
            zoom: 12
        });
        map.addControl(new maplibregl.NavigationControl(), 'top-right');
    }

    /** Tải toàn bộ danh sách shipper lần đầu */
    async function loadAllShippers() {
        document.getElementById('loading-spinner').style.display = 'block';
        try {
            const response = await fetch(API_URL);
            allShippers = await response.json();
            renderTable(allShippers);
            updateActiveShipperMarkers();
        } catch (error) {
            console.error("Lỗi khi tải danh sách shipper:", error);
        } finally {
            document.getElementById('loading-spinner').style.display = 'none';
        }
    }

    /** Render lại bảng dữ liệu dựa trên danh sách shipper được cung cấp */
    function renderTable(shippers) {
        const tableBody = document.getElementById('shipper-table-body');
        tableBody.innerHTML = '';
        if (shippers.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Không tìm thấy shipper nào.</td></tr>';
            return;
        }

        shippers.forEach((shipper, index) => {
            const statusBadge = getStatusBadge(shipper.status);
            const row = `
                <tr id="shipper-row-${shipper.ID}" style="cursor: pointer;">
                    <td>${index + 1}</td>
                    <td>${shipper.Username}</td>
                    <td>${shipper.PhoneNumber}</td>
                    <td>${statusBadge}</td>
                    <td>${shipper.rating || 'Chưa có'} ⭐</td>
                    <td>
                        <button class="btn btn-info btn-sm btn-detail" data-id="${shipper.ID}" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="index.php?updateUser&id=${shipper.ID}" class="btn btn-warning btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="index.php?deleteUser&id=${shipper.ID}" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa shipper này?');">
                            <i class="fas fa-trash"></i>
                        </a>
                        <a href="index.php?shipper_stats&id=${shipper.ID}" class="btn btn-success btn-sm" title="Thống kê">
                            <i class="fas fa-chart-line"></i>
                        </a>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    /** Cập nhật các marker của shipper đang hoạt động trên bản đồ */
    async function updateActiveShipperMarkers() {
        try {
            const response = await fetch(`${API_URL}?status=online`);
            const onlineShippers = await response.json();
            const responseBusy = await fetch(`${API_URL}?status=busy`);
            const busyShippers = await responseBusy.json();
            const activeShippers = [...onlineShippers, ...busyShippers];

            Object.values(shipperMarkers).forEach(m => m.updated = false);

            activeShippers.forEach(shipper => {
                const position = [parseFloat(shipper.lng), parseFloat(shipper.lat)];

                if (shipperMarkers[shipper.ID]) {
                    shipperMarkers[shipper.ID].marker.setLngLat(position);
                    shipperMarkers[shipper.ID].updated = true;
                } else {
                    const el = document.createElement('div');
                    el.className = 'marker';
                    el.style.backgroundImage = shipper.status === 'busy' 
                        ? `url(https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQKuXtJr9JP2Vy8DvRkphf9GsJb4b-JE3Xi_Q&s)` // Icon xe máy màu cam
                        : `url(https://cdn-icons-png.flaticon.com/512/5860/5860579.png)`; // Icon xe máy màu xanh
                    el.style.width = '35px';
                    el.style.height = '35px';
                    el.style.backgroundSize = '100%';

                    const popup = new maplibregl.Popup({ offset: 25 })
                        .setHTML(`<strong>${shipper.Username}</strong><br/>${shipper.PhoneNumber}`);

                    const marker = new maplibregl.Marker(el)
                        .setLngLat(position)
                        .setPopup(popup)
                        .addTo(map);

                    shipperMarkers[shipper.ID] = { marker, updated: true };
                }
            });

            for (const id in shipperMarkers) {
                if (!shipperMarkers[id].updated) {
                    shipperMarkers[id].marker.remove();
                    delete shipperMarkers[id];
                }
            }
        } catch (error) {
            console.error("Lỗi khi cập nhật marker:", error);
        }
    }
    
    /** Gắn các sự kiện cho bộ lọc, tìm kiếm và các nút */
    function setupEventListeners() {
        document.getElementById('filter-status').addEventListener('change', handleFilterAndSearch);
        document.getElementById('search-shipper').addEventListener('input', handleFilterAndSearch);
        
        document.getElementById('shipper-table-body').addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (!row) return;

            const shipperId = row.id.replace('shipper-row-', '');

            if (e.target.closest('.btn-detail')) {
                const shipperData = allShippers.find(s => s.ID == shipperId);
                if (shipperData) showDetailModal(shipperData);
            } else {
                highlightAndPan(shipperId);
            }
        });
    }

    /** Xử lý khi người dùng thay đổi bộ lọc hoặc gõ vào ô tìm kiếm */
    function handleFilterAndSearch() {
        const statusValue = document.getElementById('filter-status').value;
        const searchValue = document.getElementById('search-shipper').value.toLowerCase();

        const filteredShippers = allShippers.filter(shipper => {
            const matchesStatus = (statusValue === 'all') || (shipper.status === statusValue);
            const matchesSearch = shipper.Username.toLowerCase().includes(searchValue) || shipper.PhoneNumber.includes(searchValue);
            return matchesStatus && matchesSearch;
        });

        renderTable(filteredShippers);
    }

    /** Hiển thị modal với thông tin chi tiết của shipper */
    function showDetailModal(shipper) {
        document.getElementById('detail-name-title').textContent = shipper.Username;
        document.getElementById('detail-email').textContent = shipper.Email || 'Chưa cập nhật';
        document.getElementById('detail-phone').textContent = shipper.PhoneNumber || 'Chưa cập nhật';
        document.getElementById('detail-status').innerHTML = getStatusBadge(shipper.status);
        document.getElementById('detail-license').textContent = shipper.license_plate || 'Chưa cập nhật';
        document.getElementById('detail-vehicle').textContent = shipper.vehicle_model || 'Chưa cập nhật';
        document.getElementById('detail-rating').innerHTML = shipper.rating ? `<span class="text-warning">${shipper.rating} <i class="fas fa-star"></i></span>` : 'Chưa có';
        document.getElementById('detail-updated').textContent = shipper.updated_at ? new Date(shipper.updated_at).toLocaleString('vi-VN') : 'Không có';
        
        $('#shipperDetailModal').modal('show');
    }

    /** Tạo badge trạng thái Bootstrap */
    function getStatusBadge(status) {
        switch (status) {
            case 'online': return '<span class="badge badge-success">Online</span>';
            case 'busy': return '<span class="badge badge-warning">Busy</span>';
            default: return '<span class="badge badge-secondary">Offline</span>';
        }
    }

    /** Tô sáng hàng trong bảng và di chuyển bản đồ đến marker tương ứng */
    function highlightAndPan(shipperId) {
        document.querySelectorAll('#shipper-table-body tr').forEach(r => r.classList.remove('table-primary'));
        const row = document.getElementById(`shipper-row-${shipperId}`);
        if (row) {
            row.classList.add('table-primary');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        if (shipperMarkers[shipperId]) {
            map.flyTo({
                center: shipperMarkers[shipperId].marker.getLngLat(),
                zoom: 16
            });
        }
    }
</script>