<?php

if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 1 && $_SESSION["role"] != 2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    echo "<script>window.location.href = 'index2.php';</script>"; 
    exit();
}
?>
<div class="container-fluid" id="staff" style="margin-top: 20px;">
<h1 class="h3 mb-4 text-gray-800 text-center">Quản Lý Nhân Viên</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên</h6>
        
        <div class="d-flex align-items-center">
            <form id="search-form" class="d-inline-flex mr-3">
                <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm nhân viên..." class="form-control" style="width: 300px;">
                <button type="submit" class="btn btn-primary ml-2">Tìm</button>
            </form>
            
            <a href="?listCustomer" class="btn btn-outline-primary mr-2">DS Khách hàng</a>
            <a href="?addUser" class="btn btn-success">+ Thêm Mới</a>
        </div>
    </div>
    <div class="card-body">
        <div id="table-loading-spinner" class="text-center p-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="staffTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>STT</th> <th>Mã NV</th>
                        <th>Họ Tên</th>
                        <th>SĐT</th>
                        <th>Email</th>
                        <th>Chức vụ</th>
                        <th>Tài khoản</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="staffTableBody">
                    </tbody>
            </table>
        </div>
        
        <nav id="pagination-container" aria-label="Page navigation" style="background-color: white;">
            <ul class="pagination justify-content-center" id="pagination-ul">
                </ul>
        </nav>
    </div>
</div>
</div>

<script>
    const tableBody = document.getElementById('staffTableBody');
    const paginationContainer = document.getElementById('pagination-ul');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('searchInput');
    const spinner = document.getElementById('table-loading-spinner');

    let searchTimeout; 

    /**
     * Hàm chính: Lấy dữ liệu từ API
     */
    async function fetchAndRenderUsers(page = 1, search = null) {
        spinner.style.display = 'block';
        tableBody.innerHTML = '';
        paginationContainer.innerHTML = '';

        let apiUrl = 'api/user/user.php?';
        const params = new URLSearchParams();
        params.append('page', page);
        if (search && search.trim() !== '') {
            params.append('search', search.trim());
        }
        apiUrl += params.toString();

        try {
            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error(`Lỗi HTTP: ${response.status}`);
            const result = await response.json();

            // NÂNG CẤP: Truyền thêm trang hiện tại và giới hạn vào renderTable
            renderTable(result.data, result.pagination.current_page, result.pagination.limit);
            renderPagination(result.pagination, search);
        } catch (error) {
            console.error("Lỗi khi tải dữ liệu:", error);
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Có lỗi xảy ra khi tải dữ liệu.</td></tr>'; // Sửa colspan
        } finally {
            spinner.style.display = 'none';
        }
    }

    /**
     * Vẽ lại nội dung bảng (ĐÃ NÂNG CẤP)
     */
    function renderTable(staffs, currentPage, limitPerPage) { // <-- Thêm tham số
        if (!staffs || staffs.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center"> <div class="alert alert-warning" role="alert">
                            Không có nhân viên nào được tìm thấy.
                        </div>
                    </td>
                </tr>`;
            return;
        }

        staffs.forEach((staff, index) => {
            // TÍNH TOÁN STT DỰA TRÊN TRANG VÀ GIỚI HẠN
            const stt = (currentPage - 1) * limitPerPage + index + 1;

            const statusBadge = getAccountStatusBadge(staff.account_status);
            
            let toggleButton = '';
            if (staff.account_status == 'active') {
                toggleButton = `<a href="?toggleUserStatus&id=${staff.ID}&status=active&return=quanlyuser" onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản này?');" class="btn btn-warning btn-sm" title="Khóa tài khoản"><i class="fas fa-lock"></i></a>`;
            } else {
                toggleButton = `<a href="?toggleUserStatus&id=${staff.ID}&status=${staff.account_status}&return=quanlyuser" onclick="return confirm('Bạn có chắc chắn muốn KÍCH HOẠT tài khoản này?');" class="btn btn-info btn-sm" title="Kích hoạt tài khoản"><i class="fas fa-lock-open"></i></a>`;
            }

            tableBody.innerHTML += `
                <tr>
                    <td>${stt}</td> <td>${staff.ID}</td>
                    <td>${staff.Username}</td>
                    <td>${staff.PhoneNumber}</td>
                    <td>${staff.Email}</td>
                    <td>${staff.RoleName}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <a href="?updateUser&id=${staff.ID}" class="btn btn-success btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${toggleButton}
                        <a href="?deleteUser&id=${staff.ID}" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');" class="btn btn-danger btn-sm" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }
    
    // (Các hàm getAccountStatusBadge, renderPagination, setupEventListeners... giữ nguyên)
    
    function getAccountStatusBadge(status) {
        let badgeClass = 'badge-secondary';
        let statusText = status;
        switch (status) {
            case 'active': badgeClass = 'badge-success'; statusText = 'Hoạt động'; break;
            case 'locked': badgeClass = 'badge-danger'; statusText = 'Đã khóa'; break;
            case 'pending': badgeClass = 'badge-warning'; statusText = 'Chờ duyệt'; break;
        }
        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }

    function renderPagination(pagination, search) {
        if (!pagination || pagination.total_pages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        let html = '';
        const currentPage = pagination.current_page;
        const totalPages = pagination.total_pages;
        const searchTerm = search || ''; 
        html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">Trước</a>
        </li>`;
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i == currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
        html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">Sau</a>
        </li>`;
        paginationContainer.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchAndRenderUsers();
    });

    searchForm.addEventListener('submit', (e) => {
        e.preventDefault(); 
        clearTimeout(searchTimeout);
        const searchTerm = searchInput.value;
        fetchAndRenderUsers(1, searchTerm);
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout); 
        const searchTerm = searchInput.value;
        searchTimeout = setTimeout(() => {
            fetchAndRenderUsers(1, searchTerm);
        }, 500); 
    });

    paginationContainer.addEventListener('click', (e) => {
        e.preventDefault(); 
        if (e.target.tagName === 'A' && e.target.hasAttribute('data-page')) {
            const page = parseInt(e.target.getAttribute('data-page'));
            const currentSearch = searchInput.value;
            if (page) {
                fetchAndRenderUsers(page, currentSearch);
            }
        }
    });
</script>