<?php
$today = time();
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="table-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="header-title">ตารางจัดส่งแท่งเงิน</h3>
                            <p class="header-subtitle">Bowins Design Delivery Schedule</p>
                        </div>
                    </div>
                </div>

                <div class="filter-section">
                    <?php $today = time(); ?>
                    <div class="btn-area btn-group mb-3">
                        <form name="filter" class="form-inline mr-2" onsubmit="return false;">
                            <div class="form-group mr-3">
                                <label class="filter-label mr-2">
                                    <i class="fas fa-calendar-day mr-1"></i>From
                                </label>
                                <input name="from" type="date" class="form-control custom-date-input" value="<?php echo date("Y-m-d", $today); ?>" max="<?php echo date("Y-m-d", $today + (86400 * 30)); ?>">
                            </div>
                            <div class="form-group mr-3">
                                <label class="filter-label mr-2">
                                    <i class="fas fa-calendar-day mr-1"></i>To
                                </label>
                                <input name="to" type="date" class="form-control custom-date-input" value="<?php echo date("Y-m-d", $today + (86400 * 30)); ?>" min="<?php echo date("Y-m-d", $today); ?>">
                            </div>
                            <button type="button" class="btn btn-lookup" onclick='$("#tblOrder").DataTable().draw();'>
                                <i class="fas fa-search mr-2"></i>ค้นหา
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-content">
                    <div class="table-responsive">
                        <table id="tblOrder" class="table table-sm table-striped table-bordered table-hover table-middle custom-datatable" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center hidden-xs">
                                        <span control="chk_order" class="far fa-lg fa-square check-all-btn" style="cursor:pointer;"></span>
                                    </th>
                                    <th class="text-center th-status"></th>
                                    <th class="text-center th-action"><i class="far fa-sm fa-pen"></i></th>
                                    <th class="text-center th-order">หมายเลขสั่งซื้อ</th>
                                    <th class="text-center th-delivery">หมายเลขส่งของ</th>
                                    <th class="text-center th-customer">ชื่อลูกค้า</th>
                                    <th class="text-center th-customer">User</th>
                                    <th class="text-center th-amount">จำนวน / แท่ง</th>
                                    <th class="text-center th-price">ราคา / กรัม</th>
                                    <th class="text-center th-discount">Platform</th>
                                    <th class="text-center th-total">ยอดรวม</th>
                                    <th class="text-center th-boxes">กล่อง</th>
                                    <th class="text-center th-order-date">สั่งซื้อ</th>
                                    <th class="text-center th-delivery-date">ส่งของ</th>
                                    <th class="text-center th-delivery-method">วิธีจัดส่ง</th>
                                    <th class="text-center th-postpone">เลื่อน</th>
                                    <th class="text-center th-sales">จัดเตรียม</th>
                                    <th class="text-center th-sales">ผู้ขาย</th>
                                    <th class="text-center th-sales">Tracking</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    #btnPrintThaiPost {
        pointer-events: auto !important;
    }

    #btnPrintThaiPost.is-active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0b5ed7;
    }

    #btnPrintThaiPost.is-active:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }


    #tblOrder thead th {
        position: relative;
    }

    /* ===== Badge สำหรับ orderable_type ===== */
    .badge-delivery-method {
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* ===== ปุ่มและ checkbox header แบบใหม่ ===== */

    .check-all-btn {
        font-size: 18px !important;
        color: #007bff;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        margin-right: 8px;
    }

    .check-all-btn:hover {
        color: #0056b3;
        transform: scale(1.2);
    }

    #btnPrintThaiPost {
        background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        border: none;
        color: #fff !important;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 25px;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }

    #btnPrintThaiPost:hover:not(:disabled) {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        box-shadow: 0 3px 8px rgba(59, 130, 246, 0.4);
        transform: translateY(-1px);
    }

    #btnPrintThaiPost:disabled {
        background: #dbeafe !important;
        color: #9ca3af !important;
        box-shadow: none;
        cursor: not-allowed;
    }



    /* จัดวางในหัวคอลัมน์ให้นิ่ง */
    #tblOrder thead th .th-tools {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
        /* กันโดน layer อื่นทับ */
    }

    .check-all-btn {
        cursor: pointer;
        color: #fff;
        opacity: .9;
    }

    .check-all-btn:hover {
        opacity: 1;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 32, 78, 0.15);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #00204E 0%, #003875 100%);
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-icon {
        background: rgba(255, 255, 255, 0.2);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .header-title {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 600;
    }

    .header-subtitle {
        margin: 0;
        opacity: 0.8;
        font-size: 0.8rem;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .filter-label {
        color: #00204E;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .custom-date-input {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .custom-date-input:focus {
        border-color: #00204E;
        box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.25);
    }

    .btn-lookup {
        background: linear-gradient(135deg, #00204E 0%, #003875 100%);
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-lookup:hover {
        background: linear-gradient(135deg, #003875 0%, #00204E 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 32, 78, 0.3);
        color: white;
    }

    .table-content {
        padding: 0;
    }

    /* DataTable Styling */
    .custom-datatable {
        margin: 0;
        border-collapse: collapse;
    }

    .custom-datatable thead th {
        background: linear-gradient(135deg, #00204E 0%, #003875 100%);
        color: white;
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-align: center;
    }

    .custom-datatable tbody td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.85rem;
    }

    .custom-datatable tbody tr:hover {
        background-color: rgba(0, 32, 78, 0.05);
    }

    .custom-datatable tbody tr.selected {
        background-color: rgba(0, 32, 78, 0.1);
    }

    /* Custom Checkbox */
    .custom-checkbox {
        width: 18px;
        height: 18px;
        accent-color: #00204E;
    }

    .check-all-btn {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .check-all-btn:hover {
        color: #ffc107;
    }

    /* Badges */
    .badge-primary {
        background-color: #00204E;
        color: white;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    /* Buttons */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 4px;
    }

    .btn-outline-primary {
        border-color: #00204E;
        color: #00204E;
    }

    .btn-outline-primary:hover {
        background-color: #00204E;
        border-color: #00204E;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-success:hover {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    /* Order Link */
    .order-link {
        color: #00204E;
        font-weight: 600;
        text-decoration: none;
    }

    .order-link:hover {
        color: #003875;
        text-decoration: underline;
    }

    /* Schedule Table */
    .schedule-table {
        background: rgba(0, 32, 78, 0.05);
        border-radius: 8px;
    }

    .schedule-day {
        background: #00204E;
        color: white;
        font-size: 0.7rem;
        padding: 0.5rem 0.25rem;
    }

    .btn-schedule-nav {
        background: #00204E;
        color: white;
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    .btn-schedule-nav:hover {
        background: #003875;
        color: white;
    }

    /* Delivery Table */
    .delivery-table {
        font-size: 0.7rem;
    }

    .delivery-active {
        color: #28a745;
    }

    .delivery-inactive {
        color: #6c757d;
    }

    /* DataTables Pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #00204E !important;
        border-color: #00204E !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #003875 !important;
        border-color: #003875 !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #00204E;
        box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.25);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table-header {
            padding: 1rem;
        }

        .filter-section {
            padding: 1rem;
        }

        .header-title {
            font-size: 1.2rem;
        }

        .custom-datatable thead th,
        .custom-datatable tbody td {
            padding: 0.5rem 0.25rem;
            font-size: 0.75rem;
        }
    }

    /* เพิ่มใน <style> */
    .th-boxes {
        min-width: 60px;
    }

    .badge-box-count {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .box-icon-single {
        color: #6c757d;
        opacity: 0.7;
    }

    .box-icon-multi {
        color: #007bff;
    }

    /* เพิ่มใน <style> */
    .table-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .tracking-number {
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .tracking-number:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .box-tracking-btn {
        transition: all 0.2s ease;
    }

    .box-tracking-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
    }

    .modal-lg {
        max-width: 900px;
    }

    @media (max-width: 768px) {
        .modal-lg {
            max-width: 95%;
        }
    }

    /* เพิ่มใน <style> */

    .shipping-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .shipping-info .badge {
        box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
    }

    #modalBoxTracking tfoot tr {
        font-weight: 600;
    }

    #modalBoxTracking tfoot.thead-light td {
        background-color: #f8f9fa;
        border-top: 2px solid #dee2e6;
    }

    .table-info {
        background-color: rgba(23, 162, 184, 0.1) !important;
    }

    .table-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }

    /* เพิ่มใน <style> */

    .tracking-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .tracking-status:hover {
        transform: scale(1.05);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    .badge-success.tracking-status {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .badge-warning.tracking-status {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #fff;
    }

    .badge-warning.tracking-status i {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }
</style>