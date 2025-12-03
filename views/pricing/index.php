<?php
    if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
        echo "<script>alert('Bạn không có quyền truy cập!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
?>
<div class="container-fluid"style="margin-top: 20px;">
     <h1 class="h3 mb-0 text-gray-800 text-center">Quản lý Bảng giá & Cước phí</h1>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 text-center"></h1>
        <button class="btn btn-success" onclick="openModal()">
            <i class="fas fa-plus fa-sm text-white-50"></i> Thêm Bảng Giá Mới
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách cấu hình giá cước</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tên Bảng Giá</th>
                            <th>Loại Xe</th>
                            <th>Giá Mở Cửa</th>
                            <th>Giá / Km</th>
                            <th>Phụ Phí Cân Nặng</th>
                            <th>Trạng Thái</th>
                            <th style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="pricing-table-body">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pricingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary font-weight-bold" id="modalTitle">Thiết lập giá cước</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="pricingForm">
                    <input type="hidden" id="ruleId">
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tên hiển thị</label>
                            <input type="text" id="ruleName" class="form-control" placeholder="VD: Giá tiêu chuẩn 2025" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Loại phương tiện</label>
                            <select id="vehicleType" class="form-control">
                                <option value="motorbike">Xe máy (Motorbike)</option>
                                <option value="truck">Xe tải (Truck)</option>
                                <option value="express">Hỏa tốc (Express)</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="text-secondary mt-2"><i class="fas fa-road"></i> Cước phí Khoảng cách</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Khoảng cách mở cửa (Km đầu)</label>
                            <input type="number" id="baseDistance" class="form-control" value="2" step="0.1">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Giá mở cửa (VNĐ)</label>
                            <input type="number" id="basePrice" class="form-control" value="15000">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Giá mỗi Km tiếp theo (VNĐ/km)</label>
                            <input type="number" id="pricePerKm" class="form-control font-weight-bold text-success" value="5000">
                        </div>
                    </div>

                    <h6 class="text-secondary mt-2"><i class="fas fa-weight-hanging"></i> Cước phí Trọng lượng</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Miễn phí trọng lượng (Kg đầu)</label>
                            <input type="number" id="freeWeight" class="form-control" value="2" step="0.5">
                            <small class="text-muted">Dưới mức này sẽ không tính thêm phí.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Phụ phí mỗi Kg thêm (VNĐ/kg)</label>
                            <input type="number" id="pricePerKg" class="form-control" value="2500">
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="isActive" checked>
                            <label class="custom-control-label font-weight-bold" for="isActive">Kích hoạt bảng giá này ngay</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveRule()">Lưu Cấu Hình</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Định dạng tiền tệ
    const fmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });

    // 1. Tải danh sách
    async function loadRules() {
        try {
            const res = await fetch('api/pricing/get_all.php');
            const data = await res.json();
            const tbody = document.getElementById('pricing-table-body');
            tbody.innerHTML = '';

            data.forEach(rule => {
                const activeBadge = rule.IsActive == 1 
                    ? '<span class="badge badge-success">Đang dùng</span>' 
                    : '<span class="badge badge-secondary">Tắt</span>';
                
                const row = `
                    <tr>
                        <td class="font-weight-bold">${rule.Name}</td>
                        <td>${getVehicleIcon(rule.VehicleType)} ${rule.VehicleType}</td>
                        <td>
                            ${fmt.format(rule.BasePrice)} <br>
                            <small class="text-muted">(${rule.BaseDistance} km đầu)</small>
                        </td>
                        <td class="text-success font-weight-bold">${fmt.format(rule.PricePerKm)}</td>
                        <td>
                            ${fmt.format(rule.PricePerKg)} /kg <br>
                            <small class="text-muted">(Trừ ${rule.FreeWeight}kg đầu)</small>
                        </td>
                        <td>${activeBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick='editRule(${JSON.stringify(rule)})'><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRule(${rule.ID})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } catch(e) { console.error(e); }
    }

    // 2. Mở Modal (Thêm hoặc Sửa)
    function openModal() {
        document.getElementById('pricingForm').reset();
        document.getElementById('ruleId').value = '';
        document.getElementById('modalTitle').innerText = 'Thêm Bảng Giá Mới';
        $('#pricingModal').modal('show');
    }

    function editRule(rule) {
        document.getElementById('ruleId').value = rule.ID;
        document.getElementById('ruleName').value = rule.Name;
        document.getElementById('vehicleType').value = rule.VehicleType;
        document.getElementById('baseDistance').value = rule.BaseDistance;
        document.getElementById('basePrice').value = rule.BasePrice;
        document.getElementById('pricePerKm').value = rule.PricePerKm;
        document.getElementById('freeWeight').value = rule.FreeWeight;
        document.getElementById('pricePerKg').value = rule.PricePerKg;
        document.getElementById('isActive').checked = (rule.IsActive == 1);
        
        document.getElementById('modalTitle').innerText = 'Cập nhật Bảng Giá';
        $('#pricingModal').modal('show');
    }

    // 3. Lưu dữ liệu
    async function saveRule() {
        const pricePerKmInput = document.getElementById('pricePerKm');
        // -----------------------------------------------------

        if (!pricePerKmInput) {
            console.error("Không tìm thấy input pricePerKm");
            return;
        }

        const data = {
            ID: document.getElementById('ruleId').value,
            Name: document.getElementById('ruleName').value,
            VehicleType: document.getElementById('vehicleType').value,
            BaseDistance: document.getElementById('baseDistance').value,
            BasePrice: document.getElementById('basePrice').value,
            PricePerKm: pricePerKmInput.value,
            FreeWeight: document.getElementById('freeWeight').value,
            PricePerKg: document.getElementById('pricePerKg').value,
            IsActive: document.getElementById('isActive').checked ? 1 : 0
        };

        if(!data.Name) { alert('Vui lòng nhập tên bảng giá'); return; }

        try {
            const res = await fetch('api/pricing/save.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if(result.success) {
                $('#pricingModal').modal('hide');
                loadRules();
                alert('Lưu thành công!');
            } else {
                alert('Lỗi: ' + result.error);
            }
        } catch(e) { 
            console.error(e);
            alert('Lỗi kết nối'); 
        }
    }

    // 4. Xóa
    async function deleteRule(id) {
        if(!confirm('Bạn có chắc chắn muốn xóa bảng giá này?')) return;
        try {
            const res = await fetch('api/pricing/delete.php', {
                method: 'POST',
                body: JSON.stringify({id: id})
            });
            const result = await res.json();
            if(result.success) loadRules();
            else alert('Lỗi khi xóa');
        } catch(e) { alert('Lỗi kết nối'); }
    }

    function getVehicleIcon(type) {
        if(type === 'truck') return '<i class="fas fa-truck"></i>';
        if(type === 'express') return '<i class="fas fa-shipping-fast"></i>';
        return '<i class="fas fa-motorcycle"></i>';
    }

    // Khởi chạy
    document.addEventListener('DOMContentLoaded', loadRules);
</script>