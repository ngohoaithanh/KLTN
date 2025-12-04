<?php
// FILE: views/incident/index.php

// 1. Kiểm tra quyền hạn (Chỉ Admin và Quản lý mới được vào)
if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 1 && $_SESSION["role"] != 2)) {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.location.href = 'index.php';</script>";
    exit();
}

// Lấy ID của Admin đang đăng nhập để ghi log khi xử lý
$current_admin_id = $_SESSION['user_id'];
?>

<style>
    /* Style cho ảnh bằng chứng trong bảng và modal */
    .proof-thumb {
        width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; cursor: pointer;
    }
    .proof-large {
        width: 100%; max-height: 400px; object-fit: contain; border: 1px solid #ddd; background: #f8f9fa; border-radius: 5px;
    }
    /* Badge trạng thái tùy chỉnh */
    .badge-pending { background-color: #f6c23e; color: #fff; }
    .badge-processing { background-color: #36b9cc; color: #fff; }
    .badge-resolved { background-color: #1cc88a; color: #fff; }
    .badge-rejected { background-color: #e74a3b; color: #fff; }
</style>

<div class="container-fluid" style="margin-top: 20px;">

    <h1 class="h3 mb-4 text-gray-800 text-center">Quản Lý Báo Cáo Sự Cố & Khiếu Nại</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách sự cố</h6>
            
            <div class="form-inline">
                <select id="filter-status" class="form-control form-control-sm mr-2">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="pending" selected>Chờ xử lý (Mới)</option>
                    <option value="processing">Đang xử lý</option>
                    <option value="resolved">Đã giải quyết</option>
                    <option value="rejected">Đã từ chối</option>
                </select>
                
                <div class="input-group input-group-sm">
                    <input type="text" id="search-input" class="form-control bg-light border-0 small" placeholder="Tìm theo mã đơn, tên..." aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="btn-search">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Mã đơn</th>
                            <th>Người báo cáo</th>
                            <th>Loại sự cố</th>
                            <th>Mô tả ngắn</th>
                            <th>Ảnh</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        </tbody>
                </table>
            </div>
            
            <div id="loading-spinner" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
            </div>

            <nav class="mt-3"style="background-color: white;">
                <ul class="pagination justify-content-center" id="pagination"></ul>
            </nav>
        </div>
    </div>

</div>

<div class="modal fade" id="incidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Xử lý Báo cáo #<span id="modal-id"></span></h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h6 class="font-weight-bold text-secondary">Thông tin sự cố</h6>
                        <p><strong>Đơn hàng:</strong> <a href="#" id="link-order" target="_blank">#<span id="modal-order-id"></span></a></p>
                        <p><strong>Người báo:</strong> <span id="modal-reporter"></span> (<span id="modal-role"></span>)</p>
                        <p><strong>Loại sự cố:</strong> <span id="modal-type" class="text-danger font-weight-bold"></span></p>
                        <p><strong>Mô tả:</strong></p>
                        <div class="p-2 bg-light rounded border mb-3" id="modal-desc" style="min-height: 60px;"></div>
                        
                        <h6 class="font-weight-bold text-secondary">Bằng chứng</h6>
                        <div class="text-center">
                            <a id="modal-img-link" href="#" target="_blank">
                                <img id="modal-img" class="proof-large" src="" alt="Không có ảnh">
                            </a>
                            <small class="text-muted d-block mt-1">* Nhấn vào ảnh để xem kích thước gốc</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-secondary">Kết quả xử lý</h6>
                        <form id="process-form">
                            <input type="hidden" id="input-id">
                            
                            <div class="form-group">
                                <label>Trạng thái xử lý <span class="text-danger">*</span></label>
                                <select id="input-status" class="form-control">
                                    <option value="pending">Chờ xử lý</option>
                                    <option value="processing">Đang xem xét</option>
                                    <option value="resolved">Đã giải quyết (Chấp nhận)</option>
                                    <option value="rejected">Từ chối (Không hợp lệ)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Hướng giải quyết / Phản hồi <span class="text-danger">*</span></label>
                                <textarea id="input-resolution" class="form-control" rows="5" placeholder="VD: Đã hoàn tiền 50% cho khách; Hoặc: Bằng chứng không đủ..."></textarea>
                            </div>

                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle"></i> Khi bấm "Lưu", hệ thống sẽ gửi thông báo kết quả đến người báo cáo.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Đóng</button>
                <button class="btn btn-primary" type="button" id="btn-save">
                    <i class="fas fa-save mr-1"></i> Lưu & Gửi thông báo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Biến toàn cục
    const ADMIN_ID = <?php echo $current_admin_id; ?>;
    let currentPage = 1;

    // Khởi chạy
    document.addEventListener('DOMContentLoaded', () => {
        fetchReports();
        
        // Gắn sự kiện lọc/tìm kiếm
        document.getElementById('filter-status').addEventListener('change', () => { currentPage = 1; fetchReports(); });
        document.getElementById('btn-search').addEventListener('click', () => { currentPage = 1; fetchReports(); });
        
        // Sự kiện lưu
        document.getElementById('btn-save').addEventListener('click', submitResolution);
    });

    // 1. Tải danh sách báo cáo
    async function fetchReports() {
        const status = document.getElementById('filter-status').value;
        const search = document.getElementById('search-input').value;
        const spinner = document.getElementById('loading-spinner');
        const tbody = document.getElementById('table-body');
        
        spinner.style.display = 'block';
        tbody.innerHTML = '';

        try {
            const res = await fetch(`api/incident/get_reports.php?page=${currentPage}&status=${status}&search=${search}`);
            const data = await res.json();
            
            renderTable(data.data);
            renderPagination(data.pagination);
        } catch(e) {
            console.error(e);
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
        } finally {
            spinner.style.display = 'none';
        }
    }

    // 2. Render Bảng
    function renderTable(reports) {
        const tbody = document.getElementById('table-body');
        if (!reports || reports.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">Không tìm thấy báo cáo nào.</td></tr>';
            return;
        }

        reports.forEach(item => {
            const date = new Date(item.Created_at).toLocaleDateString('vi-VN');
            const imgHtml = item.ProofImage 
                ? `<img src="${item.ProofImage}" class="proof-thumb" onclick="openModal(${item.ID})">` 
                : '<span class="text-muted small">Không có</span>';
            
            const row = `
                <tr>
                    <td>${item.ID}</td>
                    <td><a href="?order_detail&id=${item.OrderID}" target="_blank">#${item.OrderID}</a></td>
                    <td>
                        <strong>${item.ReporterName}</strong><br>
                        <small class="text-muted">${item.ReporterRole}</small>
                    </td>
                    <td>${getIncidentTypeBadge(item.Type)}</td>
                    <td><div class="text-truncate" style="max-width: 200px;">${item.Description}</div></td>
                    <td class="text-center">${imgHtml}</td>
                    <td>${getStatusBadge(item.Status)}</td>
                    <td>${date}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick='openModal(${JSON.stringify(item)})'>
                            <i class="fas fa-edit"></i> Xử lý
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    // 3. Mở Modal & Điền dữ liệu
    function openModal(itemOrId) {
        // Nếu tham số là ID (khi click ảnh), tìm item trong bảng (logic đơn giản hóa: reload hoặc truyền object)
        // Ở đây ta truyền object trực tiếp từ nút Xử lý. Nếu click ảnh, ta cần tìm lại (để đơn giản, click ảnh chỉ mở modal cũng được)
        
        let item = itemOrId;
        // Nếu truyền vào là ID số (từ click ảnh), ta cần tìm object đó (để đơn giản, ta bỏ qua logic này ở demo, chỉ mở từ nút Xử lý)
        if (typeof item !== 'object') return; 

        // Điền thông tin
        document.getElementById('modal-id').innerText = item.ID;
        document.getElementById('input-id').value = item.ID;
        
        document.getElementById('modal-order-id').innerText = item.OrderID;
        document.getElementById('link-order').href = `?order_detail&id=${item.OrderID}`;
        
        document.getElementById('modal-reporter').innerText = item.ReporterName;
        document.getElementById('modal-role').innerText = item.ReporterRole;
        
        document.getElementById('modal-type').innerText = item.Type;
        document.getElementById('modal-desc').innerText = item.Description;

        // Xử lý ảnh
        const imgEl = document.getElementById('modal-img');
        const linkEl = document.getElementById('modal-img-link');
        if (item.ProofImage) {
            imgEl.src = item.ProofImage;
            imgEl.style.display = 'block';
            linkEl.href = item.ProofImage;
        } else {
            imgEl.style.display = 'none';
            linkEl.removeAttribute('href');
        }

        // Điền form xử lý
        document.getElementById('input-status').value = item.Status;
        document.getElementById('input-resolution').value = item.Resolution || '';

        $('#incidentModal').modal('show');
    }

    // 4. Lưu Xử lý
    async function submitResolution() {
        const id = document.getElementById('input-id').value;
        const status = document.getElementById('input-status').value;
        const resolution = document.getElementById('input-resolution').value.trim();
        const btn = document.getElementById('btn-save');

        if (!resolution && (status == 'resolved' || status == 'rejected')) {
            alert('Vui lòng nhập hướng giải quyết hoặc lý do từ chối!');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = 'Đang lưu...';

        try {
            const res = await fetch('api/incident/update_report.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: id,
                    status: status,
                    resolution: resolution,
                    admin_id: ADMIN_ID
                })
            });
            const result = await res.json();

            if (result.success) {
                alert('Cập nhật thành công!');
                $('#incidentModal').modal('hide');
                fetchReports(); // Tải lại bảng
            } else {
                alert('Lỗi: ' + result.error);
            }
        } catch (error) {
            console.error(error);
            alert('Lỗi kết nối!');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Lưu & Gửi thông báo';
        }
    }

    // === CÁC HÀM HELPER ===

    function getStatusBadge(status) {
        switch(status) {
            case 'pending': return '<span class="badge badge-pending">Chờ xử lý</span>';
            case 'processing': return '<span class="badge badge-processing">Đang xem</span>';
            case 'resolved': return '<span class="badge badge-resolved">Đã xong</span>';
            case 'rejected': return '<span class="badge badge-rejected">Đã từ chối</span>';
            default: return status;
        }
    }

    function getIncidentTypeBadge(type) {
        // Có thể tùy biến màu sắc cho từng loại
        return `<span class="font-weight-bold text-dark">${type}</span>`;
    }

    function renderPagination(pagination) {
        const container = document.getElementById('pagination');
        container.innerHTML = '';
        if (pagination.total_pages <= 1) return;

        // Đơn giản hóa: chỉ hiện Trước/Sau
        const prevDisabled = pagination.current_page == 1 ? 'disabled' : '';
        const nextDisabled = pagination.current_page == pagination.total_pages ? 'disabled' : '';

        container.innerHTML = `
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" onclick="changePage(${pagination.current_page - 1})">Trước</a>
            </li>
            <li class="page-item active">
                <span class="page-link">${pagination.current_page} / ${pagination.total_pages}</span>
            </li>
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" onclick="changePage(${pagination.current_page + 1})">Sau</a>
            </li>
        `;
    }

    function changePage(page) {
        if (page < 1) return;
        currentPage = page;
        fetchReports();
    }
</script>