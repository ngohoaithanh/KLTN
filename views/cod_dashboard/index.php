<?php
    if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] !=1 && $_SESSION["role"] !=2 && $_SESSION["role"] !=5)) {
        echo "<script>alert('Bạn không có quyền truy cập!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
?>
<div class="container-fluid" style="margin-top: 20px;">

    <h1 class="h3 mb-4 text-gray-800 text-center">Trung tâm Đối soát Phí COD</h1>

    <div class="row" id="kpi-cards-container">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tổng Phí COD đang nợ</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-total-owed">...</div></div><div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div></div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã đối soát (Hôm nay)</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-settled-today">...</div></div><div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div></div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Phí đang chờ thu (Đang giao)</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-fee-in-progress">...</div></div><div class="col-auto"><i class="fas fa-shipping-fast fa-2x text-gray-300"></i></div></div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Phí phát sinh (Tháng này)</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-fee-this-month">...</div></div><div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div></div></div></div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bảng Công Nợ Phí COD của Shipper</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="receivables-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tên Shipper</th>
                            <th>Số điện thoại</th>
                            <th>Tổng Phí đã thu (A)</th>
                            <th>Tổng Phí đã nộp (B)</th>
                            <th>Nợ quá hạn (> 7 ngày)</th>
                            <th>Còn nợ (A-B)</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="receivables-table-body">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lịch sử Ghi nhận Giao dịch</h6>
            <div class="d-flex align-items-center">
                <label for="trans-start-date" class="mb-0 mr-2">Từ:</label>
                <input type="date" id="trans-start-date" class="form-control form-control-sm" style="width: auto;">
                <label for="trans-end-date" class="mb-0 mx-2">Đến:</label>
                <input type="date" id="trans-end-date" class="form-control form-control-sm" style="width: auto;">
                <button id="filter-trans-btn" class="btn btn-primary btn-sm ml-2">Lọc</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="transactions-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Tên Shipper</th>
                            <th>Số tiền nộp</th>
                            <th>Mã đơn (nếu có)</th>
                            <th>Loại Giao dịch</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-table-body">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logPaymentModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Ghi nhận Shipper nộp tiền</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="payment-form">
                    <input type="hidden" id="modal-shipper-id" name="shipper_id">
                    <div class="form-group">
                        <label>Tên Shipper:</label>
                        <input type="text" id="modal-shipper-name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Số tiền đang nợ:</label>
                        <input type="text" id="modal-balance-due" class="form-control" readonly>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="modal-amount-paid">Số tiền nộp</label>
                        <input type="number" id="modal-amount-paid" name="amount" class="form-control" required>
                    </div>
                     <div class="form-group">
                        <label for="modal-note">Ghi chú (Tùy chọn)</label>
                        <textarea id="modal-note" name="note" class="form-control" rows="2" placeholder="VD: Nộp tiền mặt cuối ngày..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                <button class="btn btn-primary" type="button" id="submit-payment-btn">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm định dạng tiền tệ
    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
    }

    /**
     * Tải dữ liệu toàn trang (KPI, Bảng công nợ) và Lịch sử Giao dịch.
     * Chỉ được gọi khi tải trang hoặc sau khi GHI NHẬN thanh toán.
     */
    async function loadPageData(startDate = null, endDate = null) {
        try {
            // Xác định URL API (có hoặc không có bộ lọc ngày)
            let apiUrl = 'api/cod_dashboard/get_receivables.php';
            if (startDate && endDate) {
                apiUrl += `?start_date=${startDate}&end_date=${endDate}`;
            }

            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error(`Lỗi HTTP: ${response.status}`);
            const data = await response.json();

            // 1. Render Thẻ KPI
            document.getElementById('kpi-total-owed').textContent = formatCurrency(data.kpi.TotalFeeOwed);
            document.getElementById('kpi-settled-today').textContent = formatCurrency(data.kpi.SettledToday);
            document.getElementById('kpi-fee-in-progress').textContent = formatCurrency(data.kpi.FeeInProgress);
            document.getElementById('kpi-fee-this-month').textContent = formatCurrency(data.kpi.FeeThisMonth);
            
            // 2. Render Bảng Công Nợ
            const tableBody = document.getElementById('receivables-table-body');
            tableBody.innerHTML = ''; 
            
            if (!data.shipper_balances || data.shipper_balances.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Tuyệt vời! Không có shipper nào nợ phí COD.</td></tr>'; // Cập nhật colspan
            } else {
                data.shipper_balances.forEach(shipper => {
                    const balance = shipper.Balance;
                    const overdue = shipper.TotalOverdueFee;
                    
                    let balanceClass = balance > 0 ? 'text-danger font-weight-bold' : '';
                    let overdueClass = overdue > 0 ? 'text-danger font-weight-bold' : '';
                    
                    let buttonHtml = `<button class="btn btn-success btn-sm log-payment-btn" data-id="${shipper.shipper_id}" data-name="${shipper.Username}" data-balance="${balance}">Ghi nhận</button>`;
                    if (balance <= 0) {
                        buttonHtml = `<button class="btn btn-secondary btn-sm" disabled>Đã nộp</button>`;
                    }
                    
                    const row = `
                        <tr>
                            <td>${shipper.Username}</td>
                            <td>${shipper.PhoneNumber}</td>
                            <td>${formatCurrency(shipper.TotalFeeCollected)}</td>
                            <td>${formatCurrency(shipper.TotalFeePaid)}</td>
                            <td class="${overdueClass}">${formatCurrency(overdue)}</td>
                            <td class="${balanceClass}">${formatCurrency(balance)}</td>
                            <td>${buttonHtml}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }

            // 3. Render Bảng Lịch sử Giao dịch
            renderRecentTransactions(data.recent_transactions);

        } catch (error) {
            console.error('Lỗi khi tải dữ liệu trang:', error);
            alert('Lỗi nghiêm trọng khi tải dữ liệu trang. Vui lòng kiểm tra Console (F12).');
        }
    }
    
    /**
     * Chỉ tải và render lại Bảng Lịch sử Giao dịch (được gọi khi nhấn "Lọc")
     */
    async function loadHistoryData(startDate, endDate) {
         try {
            let apiUrl = `api/cod_dashboard/get_receivables.php?start_date=${startDate}&end_date=${endDate}`;
            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error(`Lỗi HTTP: ${response.status}`);
            const data = await response.json();
            
            // Chỉ render lại Bảng Lịch sử
            renderRecentTransactions(data.recent_transactions);

        } catch (error) {
            console.error('Lỗi khi tải lịch sử:', error);
            alert('Lỗi khi tải lịch sử giao dịch.');
        }
    }

    /**
     * Hàm render Bảng Lịch sử Giao dịch
     */
    function renderRecentTransactions(transactions) {
        const tableBody = document.getElementById('transactions-table-body');
        tableBody.innerHTML = '';
        
        if (!transactions || transactions.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Không có giao dịch nào trong khoảng thời gian này.</td></tr>';
            return;
        }

        transactions.forEach(tx => {
            const txTime = new Date(tx.Created_at).toLocaleString('vi-VN');
            const txAmount = formatCurrency(tx.Amount);
            const txOrderID = tx.OrderID || '---'; 
            let txTypeBadge = '';
            if (tx.Type === 'deposit_cod') {
                txTypeBadge = '<span class="badge badge-primary">Nộp phí COD</span>';
            } else if (tx.Type === 'shipping_fee') {
                txTypeBadge = '<span class="badge badge-success">Shipper thu phí VC</span>';
            }else if (tx.Type === 'collect_cod') {
                txTypeBadge = '<span class="badge badge-success">Shipper nhận tiền (COD + Phí)</span>';
            }
            const txNote = tx.Note || '';

            const row = `
                <tr>
                    <td>${txTime}</td>
                    <td>${tx.Username}</td>
                    <td>${txAmount}</td>
                    <td>${txOrderID}</td>
                    <td>${txTypeBadge}</td>
                    <td>${txNote}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    /**
     * Cài đặt các trình lắng nghe cho Modal (Ghi nhận thanh toán)
     */
    function setupModalListeners() {
        $('#receivables-table-body').on('click', '.log-payment-btn', function() {
            const shipperId = $(this).data('id');
            const shipperName = $(this).data('name');
            const balanceDue = $(this).data('balance');
            $('#modal-shipper-id').val(shipperId);
            $('#modal-shipper-name').val(shipperName);
            $('#modal-balance-due').val(formatCurrency(balanceDue));
            $('#modal-amount-paid').val(balanceDue); // Mặc định số tiền nộp = số tiền nợ
            $('#modal-note').val('');
            $('#logPaymentModal').modal('show');
        });

        $('#submit-payment-btn').on('click', async function() {
            const shipperId = $('#modal-shipper-id').val();
            const amount = $('#modal-amount-paid').val();
            const note = $('#modal-note').val();

            if (amount <= 0) {
                alert('Số tiền nộp phải lớn hơn 0.');
                return;
            }
            
            $(this).prop('disabled', true).text('Đang xử lý...');

            try {
                const response = await fetch('api/cod_dashboard/log_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        shipper_id: shipperId,
                        amount: amount,
                        note: note
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Ghi nhận thanh toán thành công!');
                    $('#logPaymentModal').modal('hide');
                    
                    // Tải lại TOÀN BỘ dữ liệu (cả bảng công nợ và bảng lịch sử)
                    const start = $('#trans-start-date').val();
                    const end = $('#trans-end-date').val();
                    loadPageData(start, end); 
                } else {
                    alert('Lỗi: ' + result.error);
                }

            } catch (error) {
                console.error('Lỗi khi gửi thanh toán:', error);
                alert('Đã xảy ra lỗi kết nối.');
            } finally {
                 $(this).prop('disabled', false).text('Xác nhận');
            }
        });
    }

    /**
     * Cài đặt bộ lọc ngày
     */
    function setupDateFilters() {
        const startDateInput = document.getElementById('trans-start-date');
        const endDateInput = document.getElementById('trans-end-date');
        const filterButton = document.getElementById('filter-trans-btn');

        // Set giá trị mặc định cho bộ lọc (7 ngày qua)
        const today = new Date();
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(today.getDate() - 7);
        
        startDateInput.value = sevenDaysAgo.toISOString().split('T')[0];
        endDateInput.value = today.toISOString().split('T')[0];

        // Gắn sự kiện click cho nút Lọc
        filterButton.addEventListener('click', () => {
            const start = startDateInput.value;
            const end = endDateInput.value;
            if (!start || !end) {
                alert('Vui lòng chọn cả ngày bắt đầu và ngày kết thúc.');
                return;
            }
            // Chỉ tải lại dữ liệu lịch sử
            loadHistoryData(start, end);
        });
    }

    // === KHỞI CHẠY ===
    document.addEventListener('DOMContentLoaded', function() {
        // Tải dữ liệu toàn trang lần đầu (dùng ngày mặc định của API)
        loadPageData(); 
        setupModalListeners(); // Cài đặt các trình lắng nghe cho modal
        setupDateFilters(); // Cài đặt bộ lọc ngày
    });
</script>