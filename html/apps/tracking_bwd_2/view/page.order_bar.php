<?php
$today = time();
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <!-- Header -->
                <div class="table-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="header-title">ตารางใส่ Tracking No. Bowins Silver</h3>
                            <p class="header-subtitle">Silver Bar Delivery Schedule</p>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <?php $today = time(); ?>
                    <div class="btn-area btn-group mb-3">
                        <form name="filter" class="form-inline mr-2" onsubmit="return false;">
                            <div class="form-group mr-3">
                                <label class="filter-label mr-2">
                                    <i class="fas fa-calendar-day mr-1"></i>From
                                </label>
                                <input name="from" type="date" class="form-control custom-date-input" value="<?php echo date("Y-m-d", $today - 86400); ?>" max="<?php echo date("Y-m-d", $today + (86400 * 30)); ?>">
                            </div>
                            <div class="form-group mr-3">
                                <label class="filter-label mr-2">
                                    <i class="fas fa-calendar-day mr-1"></i>To
                                </label>
                                <input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", $today + (86400 * 200)); ?>" min="<?php echo date("Y-m-d", $today); ?>">
                            </div>
                            <button type="button" class="btn btn-lookup" onclick='$("#tblOrderBar").DataTable().draw();'>
                                <i class="fas fa-search mr-2"></i>ค้นหา
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-content">
                    <div class="table-responsive">
                        <table id="tblOrderBar" class="table table-sm table-striped table-bordered table-hover table-middle custom-datatable" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center hidden-xs text-white font-weight-bold">
                                        <span type="checkall" control="chk_order" class="far fa-lg fa-square"></span>
                                    </th>
                                    <!-- <th class="text-center text-white font-weight-bold"><i class="far fa-sm fa-cut"></i></th> -->
                                    <th class="text-center text-white font-weight-bold">รูปแบบการจัดส่ง</th>
                                    <th class="text-center text-white font-weight-bold">หมายเลขสั่งซื้อ</th>
                                    <th class="text-center text-white font-weight-bold">หมายเลขส่งของ</th>
                                    <th class="text-center text-white font-weight-bold">ชื่อลูกค้า</th>
                                    <th class="text-center text-white font-weight-bold">จำนวน</th>
                                    <th class="text-center text-white font-weight-bold">ราคา/กิโลกรัม</th>
                                    <th class="text-center text-white font-weight-bold">ภาษีมูลค่าเพิ่ม</th>
                                    <th class="text-center text-white font-weight-bold">ยอดรวม</th>
                                    <th class="text-center text-white font-weight-bold">วันที่สั่งซื้อ</th>
                                    <th class="text-center text-white font-weight-bold">วันที่ส่งของ</th>
                                    <th class="text-center text-white font-weight-bold">Tracking</th>
                                    <th class="text-center text-white font-weight-bold">ผู้ขาย</th>
                                    <th class="text-center text-white font-weight-bold" id="schedule_header">

                                    </th>
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
</style>