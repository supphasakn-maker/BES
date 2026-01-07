<?php
global $dbc, $os;

ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    ob_clean();
    header('Content-Type: application/json');

    try {
        switch ($_POST['action']) {
            case 'update_coupons':
                if (!isset($_POST['coupons']) || empty($_POST['coupons'])) {
                    throw new Exception('ไม่มีข้อมูลสำหรับอัพเดท');
                }

                $coupons_data = json_decode($_POST['coupons'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('ข้อมูล JSON ไม่ถูกต้อง');
                }

                if (empty($coupons_data)) {
                    throw new Exception('ไม่มีข้อมูล coupon สำหรับอัพเดท');
                }

                $order_ids = array_column($coupons_data, 'order_id');
                $order_ids_escaped = array_map('addslashes', $order_ids);
                $order_ids_str = "'" . implode("','", $order_ids_escaped) . "'";


                $updated_count = 0;
                foreach ($coupons_data as $coupon) {
                    $coupon_id = intval($coupon['id']);
                    $order_id = addslashes(trim($coupon['order_id']));

                    $sql = "UPDATE bs_coupons SET order_id = '$order_id', status = 0 WHERE id = $coupon_id";
                    $result = $dbc->Query($sql);

                    if ($result) {
                        $updated_count++;
                    }
                }

                echo json_encode([
                    'success' => true,
                    'message' => "อัพเดทข้อมูล $updated_count coupon(s) สำเร็จ!",
                    'updated_count' => $updated_count
                ]);
                break;

            case 'get_coupons':
                $sql = "SELECT id, number, order_id, created, status FROM bs_coupons ORDER BY id ASC";
                $result = $dbc->Query($sql);

                $coupons = [];
                if ($result) {
                    while ($row = $dbc->Fetch($result)) {
                        $coupons[] = [
                            'id' => intval($row['id']),
                            'number' => $row['number'],
                            'order_id' => $row['order_id'],
                            'created' => $row['created'],
                            'status' => intval($row['status'])
                        ];
                    }
                }

                echo json_encode([
                    'success' => true,
                    'data' => $coupons,
                    'total' => count($coupons)
                ]);
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

$coupons_data = [];
try {
    $sql = "SELECT id, number, order_id, created, status FROM bs_coupons ORDER BY id ASC";
    $result = $dbc->Query($sql);

    if ($result === false) {
        throw new Exception('Query failed: SELECT bs_coupons');
    }

    while ($row = $dbc->Fetch($result)) {
        $coupons_data[] = [
            'id' => (int)$row['id'],
            'number' => $row['number'],
            'order_id' => $row['order_id'],
            'created' => $row['created'],
            'status' => (int)$row['status'],
        ];
    }
} catch (Exception $e) {
    echo '<div style="color:red;font-weight:600">ERROR: ' . $e->getMessage() . '</div>';
    $coupons_data = [];
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบตรวจสอบ Coupons</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding: 2rem 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }

        .card-header {
            background: #00204E;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .table-container {
            max-height: none;
            overflow-y: visible;
        }

        .table {
            margin: 0;
        }

        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table td {
            vertical-align: middle;
            border-color: #dee2e6;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #00204E;
            box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.25);
        }

        .btn-primary {
            background: #00204E;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #001a3d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 32, 78, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 8px;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-used {
            background: #f8d7da;
            color: #721c24;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .alert {
            border: none;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .summary-cards {
            margin-bottom: 2rem;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .summary-number {
            font-size: 2rem;
            font-weight: bold;
            color: #00204E;
        }

        .order-input {
            max-width: 200px;
        }

        .duplicate-error {
            border-color: #dc3545 !important;
            background-color: #fff5f5;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .row-checked {
            background-color: #f8f9fa !important;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .row-checked:hover {
            background-color: #e9ecef !important;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-ticket-alt me-2"></i>
                            ระบบตรวจสอบ Coupons
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row summary-cards">
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <div class="summary-number" id="totalCoupons">0</div>
                                    <div>Total Coupons</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <div class="summary-number text-success" id="selectedCoupons">0</div>
                                    <div>Selected</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <div class="summary-number text-warning" id="usedCoupons">0</div>
                                    <div>Used</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <!-- <button class="btn btn-primary me-2" onclick="selectAll()">
                                    <i class="fas fa-check-square me-1"></i> เลือกทั้งหมด
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearAll()">
                                    <i class="fas fa-times me-1"></i> ยกเลิกทั้งหมด
                                </button> -->
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn-primary me-2" onclick="updateCoupons()" id="updateBtn">
                                    <i class="fas fa-save me-1"></i> บันทึกการอัพเดท
                                </button>
                            </div>
                        </div>

                        <div id="alertContainer"></div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="80" class="text-center">
                                            <!-- <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()"> -->
                                        </th>
                                        <th>Coupon Number</th>
                                        <th width="200">Order ID</th>
                                        <th width="120">Created Date</th>
                                        <th width="100" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="couponsTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const couponsData = <?php echo json_encode($coupons_data ?? [], JSON_UNESCAPED_UNICODE); ?>;
        //console.log('couponsData', couponsData); 

        function initializeTable() {
            const tbody = document.getElementById('couponsTableBody');
            if (!tbody) {
                console.warn('[coupons] tbody not found');
                return;
            }

            const list = Array.isArray(window.couponsData || couponsData) ?
                (window.couponsData || couponsData) :
                [];
            tbody.innerHTML = '';

            if (list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">No data available</td></tr>';
                if (typeof updateSummary === 'function') updateSummary();
                return;
            }

            for (const c of list) {
                try {
                    const tr = document.createElement('tr');

                    const tdChk = document.createElement('td');
                    tdChk.className = 'text-center checkbox-wrapper';
                    const chk = document.createElement('input');
                    chk.type = 'checkbox';
                    chk.id = `check_${c?.id}`;
                    const isUsed = Number(c?.status) === 0; // 0 = Used, 1 = Active
                    if (isUsed) chk.disabled = true;
                    tdChk.appendChild(chk);
                    tr.appendChild(tdChk);

                    const tdNum = document.createElement('td');
                    const strong = document.createElement('strong');
                    strong.textContent = c?.number ?? '';
                    tdNum.appendChild(strong);
                    tr.appendChild(tdNum);

                    const tdOrder = document.createElement('td');
                    const inp = document.createElement('input');
                    inp.type = 'text';
                    inp.className = 'form-control order-input bg-light'; // bg-light ให้เห็นว่าแก้ไม่ได้
                    inp.id = `order_${c?.id}`;
                    inp.placeholder = 'Order ID';
                    inp.value = c?.order_id ?? '';
                    inp.readOnly = true;
                    if (isUsed) inp.disabled = true;

                    if (typeof validateOrderId === 'function') {
                        inp.addEventListener('change', () => validateOrderId(c?.id));
                        inp.addEventListener('keyup', () => validateOrderId(c?.id));
                    }

                    inp.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            if (typeof updateCoupons === 'function') updateCoupons();
                        }
                    });

                    tdOrder.appendChild(inp);

                    const err = document.createElement('div');
                    err.id = `error_${c?.id}`;
                    err.className = 'error-message';
                    err.style.display = 'none';
                    tdOrder.appendChild(err);

                    tr.appendChild(tdOrder);

                    // Created Date
                    const tdCreated = document.createElement('td');
                    tdCreated.textContent = (typeof formatDate === 'function') ?
                        formatDate(c?.created) :
                        (c?.created ?? '-');
                    tr.appendChild(tdCreated);

                    const tdStatus = document.createElement('td');
                    tdStatus.className = 'text-center';

                    const statusSpan = document.createElement('span');
                    const isActive = !isUsed;
                    statusSpan.className = `status-badge ${isActive ? 'status-active' : 'status-used'}`;
                    statusSpan.textContent = isActive ? 'Active' : 'Used';
                    tdStatus.appendChild(statusSpan);

                    if (!isActive) {
                        tdStatus.appendChild(document.createElement('br'));
                        const btn = document.createElement('button');
                        btn.className = 'btn btn-sm btn-outline-danger mt-1';
                        btn.innerHTML = '<i class="fas fa-undo"></i> Unused';
                        btn.addEventListener('click', () => {
                            if (typeof unusedCoupon === 'function') unusedCoupon(c.id);
                        });
                        tdStatus.appendChild(btn);
                    }

                    tr.appendChild(tdStatus);
                    tbody.appendChild(tr);

                    chk.addEventListener('change', () => {
                        const editable = chk.checked && !isUsed;
                        inp.readOnly = !editable;
                        inp.classList.toggle('bg-light', !editable);
                        if (typeof updateSummary === 'function') updateSummary();
                    });

                } catch (e) {
                    console.error('[coupons] row error:', c, e);
                }
            }

            if (typeof updateSummary === 'function') updateSummary();
        }

        function validateOrderId(couponId) {
            const orderInput = document.getElementById(`order_${couponId}`);
            const errorDiv = document.getElementById(`error_${couponId}`);
            const orderId = orderInput.value.trim();

            orderInput.classList.remove('duplicate-error');
            errorDiv.style.display = 'none';

            if (orderId === '') {
                return;
            }

            const isDuplicate = Array.from(document.querySelectorAll('[id^="order_"]'))
                .filter(input => input.id !== `order_${couponId}`)
                .some(input => input.value.trim() === orderId);


        }

        function selectAll() {
            const checkboxes = document.querySelectorAll('[id^="check_"]:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            document.getElementById('selectAllCheckbox').checked = true;
            updateSummary();
        }

        function clearAll() {
            const checkboxes = document.querySelectorAll('[id^="check_"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateSummary();
        }

        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('[id^="check_"]:not(:disabled)');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            updateSummary();
        }

        function updateSummary() {
            const totalCoupons = Array.isArray(couponsData) ? couponsData.length : 0;
            const selectedCount = document.querySelectorAll('[id^="check_"]:checked').length;
            const usedCount = (couponsData || []).filter(x => Number(x?.status) === 0).length;
            document.getElementById('totalCoupons').textContent = totalCoupons;
            document.getElementById('selectedCoupons').textContent = selectedCount;
            document.getElementById('usedCoupons').textContent = usedCount;
        }

        function updateCoupons() {
            const checkboxes = Array.from(document.querySelectorAll('[id^="check_"]:not(:disabled)'));
            const selected = checkboxes.filter(chk => chk.checked);

            if (selected.length === 0) {
                showAlert('กรุณาเลือกแถวที่ต้องการอัปเดทอย่างน้อย 1 รายการ', 'warning');
                return;
            }

            function clearErr(input, errDiv) {
                if (!input || !errDiv) return;
                input.classList.remove('duplicate-error', 'is-invalid');
                errDiv.style.display = 'none';
                errDiv.textContent = '';
            }

            function setErr(input, errDiv, msg) {
                if (!input || !errDiv) return;
                input.classList.add('is-invalid');
                errDiv.style.display = 'block';
                errDiv.textContent = msg || 'กรุณากรอกข้อมูล';
            }

            let hasError = false;
            let firstBad = null;
            const seen = new Set();
            const payload = [];

            for (const chk of selected) {
                const id = chk.id.replace('check_', '');
                const input = document.getElementById(`order_${id}`);
                const errDiv = document.getElementById(`error_${id}`);
                const val = (input?.value || '').trim();

                clearErr(input, errDiv);

                // ต้องกรอกเสมอ (ห้ามว่าง)
                if (!val) {
                    setErr(input, errDiv, 'กรุณาใส่ Order ID');
                    if (!hasError) firstBad = input;
                    hasError = true;
                    continue;
                }



                payload.push({
                    id,
                    order_id: val
                });
            }

            if (hasError) {
                showAlert('กรุณาแก้ไขข้อมูลที่ไฮไลต์ก่อนบันทึก', 'danger');
                if (firstBad) {
                    firstBad.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstBad.focus();
                }
                return;
            }

            // ยิง API
            const formData = new FormData();
            formData.append('action', 'update_coupons');
            formData.append('coupons', JSON.stringify(payload));

            const btn = document.getElementById('updateBtn');
            const old = btn?.innerHTML;
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> กำลังบันทึก...';
                btn.disabled = true;
            }

            fetch('apps/cou_pons/xhr/update_checkbox_status.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        showAlert(data.message || 'เกิดข้อผิดพลาดในการอัพเดท', 'danger');
                        return;
                    }
                    showAlert(data.message, 'success');

                    // อัปเดตตารางแบบเรียลไทม์ (ไม่ reload)
                    payload.forEach(({
                        id,
                        order_id
                    }) => {
                        const idx = couponsData.findIndex(c => String(c.id) === String(id));
                        if (idx !== -1) {
                            couponsData[idx].order_id = order_id;
                            couponsData[idx].status = 0; // ใช้งานแล้ว
                        }
                    });
                    initializeTable();
                    updateSummary();
                })
                .catch(err => {
                    console.error(err);
                    showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
                })
                .finally(() => {
                    if (btn) {
                        btn.innerHTML = old;
                        btn.disabled = false;
                    }
                });
        }

        function refreshCouponsTable() {
            const formData = new FormData();
            formData.append('action', 'get_coupons');

            fetch('apps/cou_pons/xhr/update_checkbox_status.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // อัปเดตตัวแปร global
                        couponsData = data.data;
                        initializeTable();
                        updateSummary();
                    }
                })
                .catch(err => console.error('Refresh error:', err));
        }

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);

            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 5000);
        }

        function unusedCoupon(couponId) {
            if (!confirm('คุณต้องการคืนสถานะคูปองนี้หรือไม่?')) return;

            const formData = new FormData();
            formData.append('action', 'unused_coupon');
            formData.append('id', couponId);

            fetch('apps/cou_pons/xhr/update_checkbox_status.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');

                        // ✅ อัปเดตฝั่ง client ทันที
                        const idx = couponsData.findIndex(c => c.id == couponId);
                        if (idx !== -1) {
                            couponsData[idx].order_id = null;
                            couponsData[idx].status = 1;
                        }

                        initializeTable();
                        updateSummary();
                    } else {
                        showAlert(data.message || 'เกิดข้อผิดพลาดในการคืนสถานะ', 'danger');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
                });
        }


        function formatDate(dateString) {
            if (!dateString) return '-';
            try {
                const iso = String(dateString).replace(' ', 'T');
                const d = new Date(iso);
                if (!isNaN(d.getTime())) return d.toLocaleDateString('th-TH');

                const [ymd] = String(dateString).split(' ');
                const [y, m, d2] = (ymd || '').split('-').map(n => parseInt(n, 10));
                if (y && m && d2) return `${String(d2).padStart(2,'0')}/${String(m).padStart(2,'0')}/${y+543}`;
                return String(dateString);
            } catch {
                return String(dateString);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeTable();
        });

        window.addEventListener('error', (e) => {
            console.error('Global error:', e.message, e.error);
        });

        function waitForElement(selector, {
            timeout = 15000
        } = {}) {
            return new Promise((resolve, reject) => {
                const found = document.querySelector(selector);
                if (found) return resolve(found);

                const obs = new MutationObserver(() => {
                    const el = document.querySelector(selector);
                    if (el) {
                        obs.disconnect();
                        resolve(el);
                    }
                });
                obs.observe(document.documentElement, {
                    childList: true,
                    subtree: true
                });

                if (timeout) {
                    setTimeout(() => {
                        obs.disconnect();
                        reject(new Error('waitForElement timeout'));
                    }, timeout);
                }
            });
        }

        let __COUPON_TABLE_RENDERED__ = false;

        function renderCouponsOnce() {
            if (__COUPON_TABLE_RENDERED__) return;
            __COUPON_TABLE_RENDERED__ = true;
            try {
                console.log('[coupons] render start');
                initializeTable();
                console.log('[coupons] render done');
            } catch (e) {
                console.error('[coupons] render error:', e);
            }
        }

        waitForElement('#couponsTableBody', {
                timeout: 30000
            })
            .then(() => renderCouponsOnce())
            .catch(err => console.warn('[coupons] tbody not found in time:', err.message));

        const spaObserver = new MutationObserver(() => {
            const tb = document.querySelector('#couponsTableBody');
            if (tb && !__COUPON_TABLE_RENDERED__) renderCouponsOnce();
        });
        spaObserver.observe(document.body, {
            childList: true,
            subtree: true
        });

        window.resetCouponsRender = function() {
            __COUPON_TABLE_RENDERED__ = false;
        };
    </script>
</body>

</html>