<style>
    /* CSS cho Marker */
    #shipper-map .marker { width: 40px; height: 40px; background-size: cover; border-radius: 50%; border: 2px solid white; cursor: pointer; }
    #shipper-map .marker-online { animation: pulse 2s infinite; }
    #shipper-map .marker-busy { border-color: #ffc107; box-shadow: 0 0 10px 3px rgba(255, 193, 7, 0.8); }
    /* @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
    } */
</style>

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
                        <input type="text" id="search-shipper" class="form-control mr-3" placeholder="Tìm theo tên/SĐT..." style="width: 250px;">
                        <a href="index.php?addUser&role=6" class="btn btn-success">
                            <i class="fas fa-plus mr-2"></i>Thêm Shipper
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="shipper-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên Shipper</th>
                                    <th>Số điện thoại</th>
                                    <th>Trạng thái Online</th> <th>Trạng thái TK</th> <th>Đánh giá</th>
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
                        <span><i class="fas fa-toggle-on fa-fw mr-3 text-gray-500"></i>Trạng thái Online</span>
                        <span id="detail-status"></span>
                    </li>
                     <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-check fa-fw mr-3 text-gray-500"></i>Trạng thái Tài khoản</span>
                        <span id="detail-account-status"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-envelope fa-fw mr-3 text-gray-500"></i>Email</span>
                        <strong id="detail-email"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-phone fa-fw mr-3 text-gray-500"></i>Số điện thoại</span>
                        <strong id="detail-phone"></strong>
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
    let allShippers = [];
    let shipperMarkers = {};
    const API_URL = 'api/shipper/getAllShippers.php';
    const GOONG_API_KEY = 'scmSgFcle8MbhKzOJMeUDIwuJWiwy6pOucLn1qQn';

    // === KHỞI TẠO ===
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        loadAllShippers(); // Tải dữ liệu lần đầu
        setupEventListeners();
        setInterval(updateShipperData, 20000); // Bật cập nhật real-time
    });

    // === CÁC HÀM XỬ LÝ ===

    function initMap() {
        map = new maplibregl.Map({
            container: 'shipper-map',
            style: `https://tiles.goong.io/assets/goong_map_web.json?api_key=${GOONG_API_KEY}`,
            center: [106.7009, 10.7769],
            zoom: 12
        });
        map.addControl(new maplibregl.NavigationControl(), 'top-right');
    }

    async function loadAllShippers() {
        document.getElementById('loading-spinner').style.display = 'block';
        try {
            const response = await fetch(API_URL);
            allShippers = await response.json();
            renderTable(allShippers);
            // Cập nhật bản đồ lần đầu
            updateShipperData(allShippers); 
        } catch (error) {
            console.error("Lỗi khi tải danh sách shipper:", error);
        } finally {
            document.getElementById('loading-spinner').style.display = 'none';
        }
    }

    // === HÀM renderTable ĐÃ NÂNG CẤP ===
    function renderTable(shippers) {
        const tableBody = document.getElementById('shipper-table-body');
        tableBody.innerHTML = '';
        if (shippers.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Không tìm thấy shipper nào.</td></tr>';
            return;
        }

        shippers.forEach((shipper, index) => {
            const onlineStatusBadge = getOnlineStatusBadge(shipper.status); // Badge cho online/offline
            const accountStatusBadge = getAccountStatusBadge(shipper.account_status); // Badge cho active/locked

            // Logic cho nút Khóa/Mở khóa
            let toggleButton = '';
            if (shipper.account_status == 'active') {
                toggleButton = `<a href="?toggleUserStatus&id=${shipper.ID}&status=active&return=quanlyshipper" onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản shipper này?');" class="btn btn-warning btn-sm" title="Khóa tài khoản"><i class="fas fa-lock"></i></a>`;
            } else {
                toggleButton = `<a href="?toggleUserStatus&id=${shipper.ID}&status=${shipper.account_status}&return=quanlyshipper" onclick="return confirm('Bạn có chắc chắn muốn KÍCH HOẠT tài khoản shipper này?');" class="btn btn-info btn-sm" title="Kích hoạt tài khoản"><i class="fas fa-lock-open"></i></a>`;
            }

            const row = `
                <tr id="shipper-row-${shipper.ID}" style="cursor: pointer;">
                    <td>${index + 1}</td>
                    <td>${shipper.Username}</td>
                    <td>${shipper.PhoneNumber}</td>
                    <td id="status-cell-online-${shipper.ID}">${onlineStatusBadge}</td> <td id="status-cell-account-${shipper.ID}">${accountStatusBadge}</td> <td>${shipper.rating || 'Chưa có'} ⭐</td>
                    <td>
                        <button class="btn btn-info btn-sm btn-detail" data-id="${shipper.ID}" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                        <a href="index.php?updateUser&id=${shipper.ID}" class="btn btn-warning btn-sm" title="Sửa"><i class="fas fa-edit"></i></a>
                        <a href="index.php?deleteUser&id=${shipper.ID}" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa shipper này?');"><i class="fas fa-trash"></i></a>
                        ${toggleButton}
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    // === HÀM CẬP NHẬT REAL-TIME ===
    async function updateShipperData(initialData = null) {
        let shippersToUpdate = initialData;

        try {
            if (!shippersToUpdate) {
                // Nếu không phải lần tải đầu, fetch dữ liệu mới
                const response = await fetch(API_URL);
                shippersToUpdate = await response.json();
                
                // Cập nhật lại mảng allShippers để bộ lọc hoạt động
                allShippers = shippersToUpdate;
            }

            Object.values(shipperMarkers).forEach(m => m.updated = false);

            shippersToUpdate.forEach(shipper => {
                // 1. CẬP NHẬT BẢNG (Cả 2 trạng thái)
                updateTableRow(shipper); 
                
                // 2. CẬP NHẬT BẢN ĐỒ
                const position = [parseFloat(shipper.lng), parseFloat(shipper.lat)];

                if (shipper.status === 'online' || shipper.status === 'busy') {
                    if (shipperMarkers[shipper.ID]) {
                        shipperMarkers[shipper.ID].marker.setLngLat(position);
                        shipperMarkers[shipper.ID].updated = true;
                    } else {
                        const el = document.createElement('div');
                        el.className = 'marker'; 
                        if (shipper.status === 'busy') {
                            el.classList.add('marker-busy');
                            el.style.backgroundImage = `url(views/img/icon_shipper.png)`;
                        } else {
                            el.classList.add('marker-online');
                            el.style.backgroundImage = `url(views/img/icon_shipper.png)`;
                        }
                        const popup = new maplibregl.Popup({ offset: 25 }).setHTML(`<strong>${shipper.Username}</strong><br/>${shipper.PhoneNumber}`);
                        const marker = new maplibregl.Marker({ element: el }).setLngLat(position).setPopup(popup).addTo(map);
                        shipperMarkers[shipper.ID] = { marker, updated: true };
                    }
                }
            });

            for (const id in shipperMarkers) {
                if (!shipperMarkers[id].updated) {
                    shipperMarkers[id].marker.remove();
                    delete shipperMarkers[id];
                }
            }
        } catch (error) {
            console.error("Lỗi khi cập nhật dữ liệu real-time:", error);
        }
    }

    // Hàm cập nhật ô trong bảng (Đã nâng cấp)
    function updateTableRow(shipper) {
        const onlineStatusCell = document.getElementById(`status-cell-online-${shipper.ID}`);
        const accountStatusCell = document.getElementById(`status-cell-account-${shipper.ID}`);
        
        if (onlineStatusCell) {
            const newBadge = getOnlineStatusBadge(shipper.status);
            if (onlineStatusCell.innerHTML !== newBadge) {
                onlineStatusCell.innerHTML = newBadge;
            }
        }
        if (accountStatusCell) {
            const newBadge = getAccountStatusBadge(shipper.account_status);
             if (accountStatusCell.innerHTML !== newBadge) {
                accountStatusCell.innerHTML = newBadge;
            }
        }
    }
    
    // === CÁC HÀM HELPER (ĐÃ THÊM) ===
    function getOnlineStatusBadge(status) {
        switch (status) {
            case 'online': return '<span class="badge badge-success">Online</span>';
            case 'busy': return '<span class="badge badge-warning">Busy</span>';
            default: return '<span class="badge badge-secondary">Offline</span>';
        }
    }

    function getAccountStatusBadge(status) {
        switch (status) {
            case 'active': return '<span class="badge badge-success">Hoạt động</span>';
            case 'locked': return '<span class="badge badge-danger">Đã khóa</span>';
            case 'pending': return '<span class="badge badge-warning">Chờ duyệt</span>';
            default: return `<span class="badge badge-secondary">${status}</span>`;
        }
    }

    // === CÁC HÀM KHÁC (Giữ nguyên) ===

    function setupEventListeners() {
        document.getElementById('filter-status').addEventListener('change', handleFilterAndSearch);
        document.getElementById('search-shipper').addEventListener('input', handleFilterAndSearch);
        
        document.getElementById('shipper-table-body').addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (!row) return;
            const shipperId = row.id.replace('shipper-row-', '');
            
            // Tìm dữ liệu shipper đầy đủ từ mảng allShippers
            const shipperData = allShippers.find(s => s.ID == shipperId);
            if (!shipperData) return;

            if (e.target.closest('.btn-detail')) {
                showDetailModal(shipperData); // Gửi dữ liệu đầy đủ
            } else {
                highlightAndPan(shipperId);
            }
        });
    }

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

    function showDetailModal(shipper) {
        document.getElementById('detail-name-title').textContent = shipper.Username;
        document.getElementById('detail-email').textContent = shipper.Email || 'Chưa cập nhật';
        document.getElementById('detail-phone').textContent = shipper.PhoneNumber || 'Chưa cập nhật';
        document.getElementById('detail-status').innerHTML = getOnlineStatusBadge(shipper.status);
        document.getElementById('detail-account-status').innerHTML = getAccountStatusBadge(shipper.account_status);
        document.getElementById('detail-license').textContent = shipper.license_plate || 'Chưa cập nhật';
        document.getElementById('detail-vehicle').textContent = shipper.vehicle_model || 'Chưa cập nhật';
        document.getElementById('detail-rating').innerHTML = shipper.rating ? `<span class="text-warning">${shipper.rating} <i class="fas fa-star"></i></span>` : 'Chưa có';
        document.getElementById('detail-updated').textContent = shipper.updated_at ? new Date(shipper.updated_at).toLocaleString('vi-VN') : 'Không có';
        
        $('#shipperDetailModal').modal('show');
    }

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