<?php
// FILE: views/quanlydonhang/index.php (PHIÊN BẢN AJAX + STT)

if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 3 && $_SESSION["role"] != 6 && $_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    echo "<script>window.location.href = 'index2.php';</script>";
    exit();
}
?>
<div class="container-fluid" id="staff" style="margin-top: 20px;">
<h1 class="h3 mb-4 text-gray-800 text-center">Quản Lý Đơn Hàng</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
        
        <div class="d-flex align-items-center">
            <form id="search-form" class="d-inline-flex mr-3">
                <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm đơn hàng..." class="form-control" style="width: 300px;">
                <button type="submit" class="btn btn-primary ml-2">Tìm</button>
            </form>
            
            <?php if ($_SESSION["role"] != 6): // Shipper không thể tự tạo đơn ?>
                <a href="?addOrder" class="btn btn-success">+ Thêm Đơn Hàng</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div id="table-loading-spinner" class="text-center p-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="ordersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>STT</th> <th>Mã đơn</th>
                        <th>Bên gửi</th>
                        <th>Bên nhận</th>
                        <th>Ngày tạo</th>
                        <th>COD</th>
                        <th>Phí VC</th>
                        <th>Phí COD</th>
                        <th>Trạng thái</th>
                        <?php if ($_SESSION["role"] != 6): ?>
                            <th>Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
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
    // Lấy các DOM element
    const tableBody = document.getElementById('ordersTableBody');
    const paginationContainer = document.getElementById('pagination-ul');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('searchInput');
    const spinner = document.getElementById('table-loading-spinner');
    const userRole = <?php echo json_encode($_SESSION['role']); ?>;
    let searchTimeout;

    /**
     * Hàm chính: Lấy dữ liệu từ API
     */
    async function fetchAndRenderOrders(page = 1, search = null) {
        spinner.style.display = 'block';
        tableBody.innerHTML = '';
        paginationContainer.innerHTML = '';

        let apiUrl = 'api/order/get_orders.php?';
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
            const colspan = (userRole != 6) ? 10 : 9; // Cập nhật colspan
            tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-danger">Có lỗi xảy ra khi tải dữ liệu.</td></tr>`;
        } finally {
            spinner.style.display = 'none';
        }
    }

    /**
     * Vẽ lại nội dung bảng (ĐÃ NÂNG CẤP)
     */
    function renderTable(orders, currentPage, limitPerPage) { // <-- Thêm tham số
        const colspan = (userRole != 6) ? 10 : 9; // Cập nhật colspan
        if (!orders || orders.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center">
                        <div class="alert alert-warning" role="alert">
                            Không có đơn hàng nào được tìm thấy.
                        </div>
                    </td>
                </tr>`;
            return;
        }

        orders.forEach((order, index) => {
            // TÍNH TOÁN STT DỰA TRÊN TRANG VÀ GIỚI HẠN
            const stt = (currentPage - 1) * limitPerPage + index + 1;
            
            const statusBadge = getStatusBadge(order.Status);

            let rowHTML = `
                <tr>
                    <td>${stt}</td> <td><a href="?order_detail&id=${order.ID}">${order.ID}</a></td>
                    <td>
                        <strong>${order.UserName || 'N/A'}</strong><br>
                        <small class="text-muted">${order.PhoneNumberCus || ''}</small><br>
                        <small>${order.Pick_up_address || ''}</small>
                    </td>
                    <td>
                        <strong>${order.Recipient || ''}</strong><br>
                        <small class="text-muted">${order.RecipientPhone || ''}</small><br>
                        <small>${order.Delivery_address || ''}</small>
                    </td>
                    <td>${order.Created_at || ''}</td>
                    <td>${formatCurrency(order.COD_amount)}</td>
                    <td>${formatCurrency(order.Shippingfee)}</td>
                    <td>${formatCurrency(order.CODFee)}</td>
                    <td>${statusBadge}</td>
            `;

            if (userRole != 6) {
                rowHTML += `
                    <td>
                        <div class="d-flex">
                            <a href="?trackOrder&order_id=${order.ID}" class="btn btn-info btn-sm mr-1" title="Theo dõi">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>
                            <a href="?updateOrder&id=${order.ID}" class="btn btn-success btn-sm mr-1" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?deleteOrder&id=${order.ID}" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');" class="btn btn-danger btn-sm" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                `;
            }
            
            rowHTML += `</tr>`;
            tableBody.innerHTML += rowHTML;
        });
    }
    
    // (Các hàm helper getStatusBadge, formatCurrency, renderPagination... giữ nguyên)
    
    function getStatusBadge(status) {
        let badgeClass = 'badge-secondary';
        let statusText = status;
        if (!status) return `<span class="badge badge-light">N/A</span>`;
        status = status.toLowerCase();

        if (status.includes('delivered')) { badgeClass = 'badge-success'; statusText = 'Đã giao';
        } else if (status.includes('in_transit') || status.includes('picked_up')) { badgeClass = 'badge-info'; statusText = 'Đang giao';
        } else if (status.includes('accepted')) { badgeClass = 'badge-primary'; statusText = 'Đã nhận';
        } else if (status.includes('pending')) { badgeClass = 'badge-warning'; statusText = 'Chờ xử lý';
        } else if (status.includes('delivery_failed') || status.includes('cancelled') || status.includes('returned')) {
            badgeClass = 'badge-danger'; 
            if(status.includes('failed')) statusText = 'Giao thất bại';
            if(status.includes('cancelled')) statusText = 'Đã hủy';
            if(status.includes('returned')) statusText = 'Hoàn trả';
        }
        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }

    function formatCurrency(number) {
        return parseInt(number || 0).toLocaleString('vi-VN') + ' VNĐ';
    }

    function renderPagination(pagination, search) {
        if (!pagination || pagination.total_pages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        let html = '';
        const currentPage = pagination.current_page;
        const totalPages = pagination.total_pages;
        html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Trước</a></li>`;
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i == currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Sau</a></li>`;
        paginationContainer.innerHTML = html;
    }

    // === GẮN CÁC SỰ KIỆN ===
    document.addEventListener('DOMContentLoaded', () => {
        fetchAndRenderOrders();
    });

    searchForm.addEventListener('submit', (e) => {
        e.preventDefault(); 
        clearTimeout(searchTimeout);
        const searchTerm = searchInput.value;
        fetchAndRenderOrders(1, searchTerm);
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout); 
        const searchTerm = searchInput.value;
        searchTimeout = setTimeout(() => {
            fetchAndRenderOrders(1, searchTerm);
        }, 500); 
    });

    paginationContainer.addEventListener('click', (e) => {
        e.preventDefault(); 
        if (e.target.tagName === 'A' && e.target.hasAttribute('data-page')) {
            const page = parseInt(e.target.getAttribute('data-page'));
            const currentSearch = searchInput.value;
            if (page) {
                fetchAndRenderOrders(page, currentSearch);
            }
        }
    });
</script>