<?php
// Chỉ cho phép Admin truy cập
if (!isset($_SESSION["dangnhap"]) || $_SESSION["role"] != 1) {
    echo "<script>alert('Chỉ Admin mới có quyền truy cập!'); window.history.back();</script>";
    exit();
}
?>

<div class="container-fluid" style="margin-top: 20px;">
    <h1 class="h3 mb-4 text-gray-800 text-center">Nhật Ký Hoạt Động Hệ Thống</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">System Logs</h6>
            
            <form id="log-search-form" class="d-inline-flex">
                <input type="text" id="log-search-input" class="form-control form-control-sm mr-2" placeholder="Tìm theo tên, hành động...">
                <button type="submit" class="btn btn-primary btn-sm">Tìm</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="logs-table" width="100%" cellspacing="0" style="font-size: 0.9rem;">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 150px;">Thời gian</th>
                            <th style="width: 150px;">Người thực hiện</th>
                            <th style="width: 100px;">Hành động</th>
                            <th style="width: 120px;">Đối tượng</th>
                            <th>Chi tiết</th>
                            <th style="width: 120px;">IP</th>
                        </tr>
                    </thead>
                    <tbody id="logs-table-body">
                        </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation" class="mt-3" style="background-color: white;">
                <ul class="pagination justify-content-center" id="logs-pagination"></ul>
            </nav>
        </div>
    </div>
</div>

<script>
    const logTableBody = document.getElementById('logs-table-body');
    const logPagination = document.getElementById('logs-pagination');
    const logSearchForm = document.getElementById('log-search-form');
    const logSearchInput = document.getElementById('log-search-input');

    // Hàm tạo badge màu cho hành động
    function getActionBadge(action) {
        let color = 'secondary';
        if (action === 'LOGIN') color = 'success';
        if (action.includes('UPDATE')) color = 'warning';
        if (action.includes('DELETE')) color = 'danger';
        if (action.includes('INSERT') || action.includes('CREATE')) color = 'primary';
        
        return `<span class="badge badge-${color}">${action}</span>`;
    }

    async function fetchLogs(page = 1, search = '') {
        logTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Đang tải...</td></tr>';
        
        try {
            let url = `api/system_log/get_logs.php?page=${page}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;

            const response = await fetch(url);
            const result = await response.json();

            renderLogs(result.data);
            renderPagination(result.pagination, search);

        } catch (error) {
            console.error(error);
            logTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
        }
    }

    function renderLogs(logs) {
        logTableBody.innerHTML = '';
        if (!logs || logs.length === 0) {
            logTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Không có dữ liệu log.</td></tr>';
            return;
        }

        logs.forEach(log => {
            const time = new Date(log.Created_at).toLocaleString('vi-VN');
            const user = log.Username ? `<span class="font-weight-bold text-dark">${log.Username}</span>` : '<em class="text-muted">Hệ thống/Lỗi</em>';
            // const target = log.TargetTable ? `${log.TargetTable} #${log.TargetID}` : '-';
            let target = '-';
            if (log.TargetTable) {
                target = log.TargetTable; // Mặc định chỉ hiện tên bảng (VD: notifications)
                
                // Chỉ thêm ID nếu nó tồn tại (khác null và khác 0)
                if (log.TargetID && log.TargetID != 0) {
                    target += ` #${log.TargetID}`;
                }
            }

            const row = `
                <tr>
                    <td>${time}</td>
                    <td>${user}</td>
                    <td>${getActionBadge(log.Action)}</td>
                    <td><small>${target}</small></td>
                    <td>${log.Description}</td>
                    <td><small class="text-muted">${log.IPAddress}</small></td>
                </tr>
            `;
            logTableBody.innerHTML += row;
        });
    }

    function renderPagination(pagination, search) {
        logPagination.innerHTML = '';
        if (!pagination || pagination.total_pages <= 1) return;

        let html = '';
        const current = pagination.current_page;
        const total = pagination.total_pages;

        // Nút Trước
        html += `<li class="page-item ${current == 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="fetchLogs(${current - 1}, '${search}'); return false;">Trước</a>
                 </li>`;

        // Các số trang (Hiển thị đơn giản)
        // Nếu nhiều trang quá có thể tối ưu hiển thị dấu ... sau
        for (let i = 1; i <= total; i++) {
            if (i == 1 || i == total || (i >= current - 2 && i <= current + 2)) {
                html += `<li class="page-item ${i == current ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="fetchLogs(${i}, '${search}'); return false;">${i}</a>
                         </li>`;
            } else if (i == current - 3 || i == current + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Nút Sau
        html += `<li class="page-item ${current == total ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="fetchLogs(${current + 1}, '${search}'); return false;">Sau</a>
                 </li>`;

        logPagination.innerHTML = html;
    }

    // Sự kiện
    logSearchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        fetchLogs(1, logSearchInput.value);
    });

    // Khởi chạy
    document.addEventListener('DOMContentLoaded', () => {
        fetchLogs();
    });
</script>