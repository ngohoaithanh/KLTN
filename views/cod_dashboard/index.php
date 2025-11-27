<?php
    if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] !=1 && $_SESSION["role"] !=2 && $_SESSION["role"] !=5)) {
        echo "<script>alert('Bạn không có quyền truy cập!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
    include_once('config/env.php');
?>
<div class="container-fluid" style="margin-top: 20px;">
    <h1 class="h3 mb-4 text-gray-800 text-center">Trung tâm Đối soát Phí COD</h1>

    <div class="row" id="kpi-cards-container">
        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-danger shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tổng Phí COD đang nợ</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-total-owed">...</div></div><div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div></div></div></div></div>
        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã đối soát (Hôm nay)</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-settled-today">...</div></div><div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div></div></div></div></div>
        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-info shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Phí đang chờ thu (Đang giao)</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-fee-in-progress">...</div></div><div class="col-auto"><i class="fas fa-shipping-fast fa-2x text-gray-300"></i></div></div></div></div></div>
        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Phí phát sinh (Tháng này)</div><div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-fee-this-month">...</div></div><div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div></div></div></div></div>
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
                    <tbody id="receivables-table-body"></tbody>
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
                            <th>Loại Giao dịch</th>
                            <th>Minh chứng</th> <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ghi nhận Shipper nộp tiền</h5>
                <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <form id="payment-form">
                    <input type="hidden" id="modal-shipper-id">
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
                        <input type="number" id="modal-amount-paid" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Minh chứng (Ảnh chuyển khoản/Biên lai)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="proof-image-input" accept="image/*">
                            <label class="custom-file-label" for="proof-image-input">Chọn ảnh...</label>
                        </div>
                        <input type="hidden" id="proof-image-url">
                        <div id="proof-preview-container" class="mt-2" style="display:none;">
                            <img id="proof-preview" src="" style="max-height: 100px; border-radius: 5px; border: 1px solid #ddd;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="modal-note">Ghi chú (Tùy chọn)</label>
                        <textarea id="modal-note" class="form-control" rows="2"></textarea>
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
    // CẤU HÌNH CLOUDINARY (Dùng lại thông tin của bạn)
    const CLOUD_NAME = "<?php echo CLOUDINARY_CLOUD_NAME; ?>";
    const UPLOAD_PRESET = "<?php echo CLOUDINARY_UPLOAD_PRESET; ?>";
    const CLOUDINARY_URL = `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`;

    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
    }

    // 1. HÀM TẢI DỮ LIỆU
    async function loadPageData(startDate = null, endDate = null) {
        try {
            let apiUrl = 'api/cod_dashboard/get_receivables.php';
            if (startDate && endDate) apiUrl += `?start_date=${startDate}&end_date=${endDate}`;

            const response = await fetch(apiUrl);
            const data = await response.json();

            if (!startDate) { 
                document.getElementById('kpi-total-owed').textContent = formatCurrency(data.kpi.TotalFeeOwed);
                document.getElementById('kpi-settled-today').textContent = formatCurrency(data.kpi.SettledToday);
                document.getElementById('kpi-fee-in-progress').textContent = formatCurrency(data.kpi.FeeInProgress);
                document.getElementById('kpi-fee-this-month').textContent = formatCurrency(data.kpi.FeeThisMonth);
                renderReceivablesTable(data.shipper_balances);
            }
            renderRecentTransactions(data.recent_transactions);
        } catch (error) {
            console.error('Lỗi tải dữ liệu:', error);
        }
    }

    // 1b. Render Bảng Công Nợ
    function renderReceivablesTable(balances) {
        const tableBody = document.getElementById('receivables-table-body');
        tableBody.innerHTML = ''; 
        if (!balances || balances.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Tuyệt vời! Không có shipper nào nợ phí COD.</td></tr>';
            return;
        }
        balances.forEach(shipper => {
            const balance = shipper.Balance;
            const overdue = shipper.TotalOverdueFee;
            let btnClass = balance > 0 ? 'btn-success' : 'btn-secondary';
            let btnDisabled = balance > 0 ? '' : 'disabled';
            
            let row = `<tr>
                <td>${shipper.Username}</td>
                <td>${shipper.PhoneNumber}</td>
                <td>${formatCurrency(shipper.TotalFeeCollected)}</td>
                <td>${formatCurrency(shipper.TotalFeePaid)}</td>
                <td class="${overdue > 0 ? 'text-danger font-weight-bold' : ''}">${formatCurrency(overdue)}</td>
                <td class="${balance > 0 ? 'text-danger font-weight-bold' : ''}">${formatCurrency(balance)}</td>
                <td>
                    <button class="btn ${btnClass} btn-sm log-payment-btn" 
                        data-id="${shipper.shipper_id}" 
                        data-name="${shipper.Username}" 
                        data-balance="${balance}" ${btnDisabled}>
                        Ghi nhận
                    </button>
                </td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    }

    // 1c. Render Bảng Lịch sử
    function renderRecentTransactions(transactions) {
        const tableBody = document.getElementById('transactions-table-body');
        tableBody.innerHTML = '';
        if (!transactions || transactions.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Không có giao dịch.</td></tr>';
            return;
        }
        transactions.forEach(tx => {
            let badge = tx.Type === 'deposit_cod' ? '<span class="badge badge-primary">Nộp phí</span>' : '<span class="badge badge-success">Nộp thừa</span>';
            
            // Xử lý hiển thị ảnh minh chứng
            let proofHtml = '<span class="text-muted small">Không có</span>';
            if (tx.ProofImage) {
                proofHtml = `<a href="${tx.ProofImage}" target="_blank" title="Xem ảnh lớn">
                                <img src="${tx.ProofImage}" style="height: 30px; border-radius: 3px; border: 1px solid #ddd;">
                             </a>`;
            }

            let row = `<tr>
                <td>${new Date(tx.Created_at).toLocaleString('vi-VN')}</td>
                <td>${tx.Username}</td>
                <td>${formatCurrency(tx.Amount)}</td>
                <td>${badge}</td>
                <td class="text-center">${proofHtml}</td>
                <td>${tx.Note || ''}</td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    }

    // 2. SỰ KIỆN
    function setupListeners() {
        // Mở modal
        $('#receivables-table-body').on('click', '.log-payment-btn', function() {
            $('#modal-shipper-id').val($(this).data('id'));
            $('#modal-shipper-name').val($(this).data('name'));
            $('#modal-balance-due').val(formatCurrency($(this).data('balance')));
            $('#modal-amount-paid').val($(this).data('balance'));
            $('#modal-note').val('');
            
            // Reset phần ảnh
            $('#proof-image-input').val('');
            $('#proof-image-url').val('');
            $('#proof-preview-container').hide();
            $('.custom-file-label').text('Chọn ảnh...');
            
            $('#logPaymentModal').modal('show');
        });
        
        // Xem trước ảnh khi chọn
        $('#proof-image-input').on('change', function() {
            const file = this.files[0];
            if (file) {
                // Validate
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    alert('Vui lòng chỉ chọn file ảnh!');
                    $(this).val(''); // Reset input
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) { // 5MB
                     alert('File ảnh quá lớn!');
                     $(this).val('');
                     return;
                }

                $('.custom-file-label').text(file.name);
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#proof-preview').attr('src', e.target.result);
                    $('#proof-preview-container').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Xử lý nút Xác nhận (Upload + Submit)
        $('#submit-payment-btn').on('click', async function() {
            const btn = $(this);
            const originalText = btn.text();
            const amount = $('#modal-amount-paid').val();
            const fileInput = document.getElementById('proof-image-input');
            const file = fileInput.files[0];

            if (amount <= 0) { alert('Số tiền phải lớn hơn 0'); return; }

            btn.prop('disabled', true).text('Đang xử lý...');

            try {
                let proofUrl = '';
                
                // A. Upload ảnh nếu có
                if (file) {
                    btn.text('Đang tải ảnh...');
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('upload_preset', UPLOAD_PRESET);
                    formData.append('folder', 'transaction_proofs');
                    
                    const cloudRes = await fetch(CLOUDINARY_URL, { method: 'POST', body: formData });
                    if (!cloudRes.ok) throw new Error('Lỗi upload ảnh');
                    const cloudData = await cloudRes.json();
                    proofUrl = cloudData.secure_url;
                }

                // B. Gửi dữ liệu về API PHP
                btn.text('Đang lưu...');
                const apiRes = await fetch('api/cod_dashboard/log_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        shipper_id: $('#modal-shipper-id').val(),
                        amount: amount,
                        note: $('#modal-note').val(),
                        proof_image: proofUrl // Gửi URL ảnh
                    })
                });
                
                const result = await apiRes.json();
                
                if (result.success) {
                    alert('Đã ghi nhận giao dịch thành công!');
                    $('#logPaymentModal').modal('hide');
                    // Tải lại dữ liệu
                    const start = $('#trans-start-date').val();
                    const end = $('#trans-end-date').val();
                     loadPageData(); 
                    loadPageData(start, end);
                    // loadHistoryData(start, end); // Nếu tách hàm
                } else {
                    alert('Lỗi: ' + result.error);
                }

            } catch (error) {
                console.error(error);
                alert('Có lỗi xảy ra.');
            } finally {
                btn.prop('disabled', false).text(originalText);
            }
        });
        
        // Bộ lọc ngày
        $('#filter-trans-btn').on('click', function() {
            const start = $('#trans-start-date').val();
            const end = $('#trans-end-date').val();
            loadHistoryData(start, end);
        });
    }
    
    // Hàm riêng để chỉ tải lịch sử (khi lọc)
    async function loadHistoryData(start, end) {
        try {
            const res = await fetch(`api/cod_dashboard/get_receivables.php?start_date=${start}&end_date=${end}`);
            const data = await res.json();
            renderRecentTransactions(data.recent_transactions);
        } catch(e) { console.error(e); }
    }

    // KHỞI CHẠY
    document.addEventListener('DOMContentLoaded', function() {
        // Set ngày mặc định cho bộ lọc
        const today = new Date();
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(today.getDate() - 7);
        document.getElementById('trans-start-date').value = sevenDaysAgo.toISOString().split('T')[0];
        document.getElementById('trans-end-date').value = today.toISOString().split('T')[0];

        loadPageData();
        setupListeners();
    });
</script>