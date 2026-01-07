<div class="row gutters-sm">
    <div class="col-12 mb-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt"></i> เลือกวันที่และภาพรวม
                </h5>
            </div>
            <div class="card-body">
                <form class="form-inline mb-3" id="dateFilterForm">
                    <div class="form-group mr-3">
                        <label class="mr-2">เลือกวันที่:</label>
                        <input name="date" type="date" class="form-control" value="<?php echo $date; ?>" id="dateInput">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-search"></i> อัปเดต
                        </button>
                        <button type="button" class="btn btn-outline-info ml-2" id="btnToday">
                            <i class="fas fa-calendar-day"></i> วันนี้
                        </button>
                    </div>
                </form>

                <div id="overviewContent">
                    <?php include "view/card.overview.php"; ?>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-truck"></i> การจัดส่งวันนี้
                </h5>
            </div>
            <div class="card-body">
                <div id="deliveryContent">
                    <?php include "view/card.delivery.php"; ?>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock"></i> ภาพรวมการจัดส่ง
                </h5>
            </div>
            <div class="card-body">
                <div id="deliveryFutureContent">
                    <?php include "view/card.delivery_future.php"; ?>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line"></i> ยอดขาย
                </h5>
            </div>
            <div class="card-body">
                <div id="salesContent">
                    <?php include "view/card.sales.php"; ?>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trash-alt"></i> รายการที่ลบ
                </h5>
            </div>
            <div class="card-body">
                <div id="removedContent">
                    <?php include "view/card.removed.php"; ?>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-undo"></i> การขายคืน
                </h5>
            </div>
            <div class="card-body">
                <div id="saleBackContent">
                    <?php include "view/card.sale_back.php"; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading States -->
<style>
    /* Card Loading State */
    .card-loading {
        position: relative;
        opacity: 0.6;
        pointer-events: none;
    }

    .card-loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100;
    }

    .card-loading::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #00204E;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 101;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Card Header Styling */
    .card-header {
        background-color: #e8f0fe;
        border-bottom: 1px solid #00204E;
        padding: 0.75rem 1rem;
    }

    .card-header .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #00204E;
    }

    .card-header .card-title i {
        margin-right: 0.5rem;
        color: #00204E;
    }

    /* Form Styling */
    .form-inline .form-group {
        margin-bottom: 0.5rem;
    }

    .form-inline .form-group label {
        font-weight: 500;
        color: #495057;
    }

    /* Date Input Styling */
    input[type="date"] {
        cursor: pointer;
        min-width: 150px;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        padding: 4px;
        border-radius: 3px;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        background-color: #e9ecef;
    }

    input[type="date"]:focus {
        border-color: #00204E;
        box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.25);
    }

    /* Button Styling */
    .btn {
        font-weight: 500;
    }

    .btn i {
        margin-right: 0.25rem;
    }

    .btn-warning {
        background-color: #00204E;
        border-color: #00204E;
        color: #ffffff;
    }

    .btn-warning:hover {
        background-color: #001a42;
        border-color: #001638;
        color: #ffffff;
    }

    .btn-warning:focus,
    .btn-warning.focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.5);
    }

    /* Card Content Styling */
    .card-body {
        padding: 1rem;
    }

    /* Card Hover Effects */
    .card {
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.15);
    }

    /* Status Indicators for BWD (Navy Blue theme) */
    .status-today {
        border-left: 4px solid #00204E;
    }

    .status-future {
        border-left: 4px solid #003d7a;
    }

    .status-sales {
        border-left: 4px solid #0066cc;
    }

    .status-removed {
        border-left: 4px solid #dc3545;
    }

    .status-return {
        border-left: 4px solid #6610f2;
    }

    /* BWD specific styling */
    .bwd-theme {
        background: linear-gradient(135deg, #e8f0fe 0%, #c8d9f7 100%);
    }

    .historical-banner.bwd {
        background-color: #e8f0fe;
        border-color: #00204E;
        color: #00204E;
        border-left: 4px solid #00204E;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Button loading state */
    .btn.loading {
        position: relative;
        pointer-events: none;
    }

    .btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* Responsive Design for Tablet/iPad */
    @media (max-width: 1024px) {
        .form-inline {
            flex-direction: column;
            align-items: stretch;
        }

        .form-inline .form-group {
            width: 100%;
            margin-bottom: 1rem;
            margin-right: 0 !important;
        }

        .form-inline .form-group label {
            display: block;
            margin-bottom: 0.25rem;
        }

        .form-inline .form-group .btn {
            width: 100%;
            margin-left: 0 !important;
            margin-top: 0.5rem;
        }

        .card-header .card-title {
            font-size: 1.1rem;
        }

        input[type="date"] {
            min-width: 100%;
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .card-header .card-title {
            font-size: 0.95rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .form-group label {
            font-size: 0.9rem;
        }
    }

    /* iPad Pro Landscape */
    @media (min-width: 1024px) and (max-width: 1366px) {
        .card {
            max-width: 100%;
            margin: 0 auto;
        }

        .card-body {
            padding: 1.25rem;
        }
    }

    /* iPad Portrait Optimization */
    @media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
        .card {
            margin-bottom: 1rem;
        }

        .card-header .card-title {
            font-size: 1.05rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // วิธีการสำหรับ Hash-based routing
        $('#dateFilterForm').on('submit', function(e) {
            e.preventDefault();

            const selectedDate = $('#dateInput').val();
            if (!selectedDate) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'กรุณาเลือกวันที่',
                        text: 'โปรดเลือกวันที่ที่ต้องการดูข้อมูล',
                        confirmButtonText: 'ตกลง'
                    });
                } else {
                    alert('กรุณาเลือกวันที่');
                }
                return;
            }

            // Validate date format
            if (!isValidDate(selectedDate)) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'รูปแบบวันที่ไม่ถูกต้อง',
                        text: 'กรุณาเลือกวันที่ที่ถูกต้อง',
                        confirmButtonText: 'ตกลง'
                    });
                } else {
                    alert('รูปแบบวันที่ไม่ถูกต้อง');
                }
                return;
            }

            // แสดง loading
            showLoadingState();

            // สร้าง URL สำหรับ hash-based routing
            const baseUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            const hashUrl = '#apps/sales_overview_bwd/index.php?date=' + encodeURIComponent(selectedDate);
            const newUrl = baseUrl + hashUrl;

            // ไปหน้าใหม่ด้วย hash URL
            window.location.href = newUrl;
        });

        // ปุ่มวันนี้
        $('#btnToday').on('click', function() {
            const today = new Date().toISOString().split('T')[0];
            const currentDate = $('#dateInput').val();

            if (currentDate === today) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'ข้อมูลปัจจุบัน',
                        text: 'คุณกำลังดูข้อมูลวันนี้อยู่แล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('คุณกำลังดูข้อมูลวันนี้อยู่แล้ว');
                }
                return;
            }

            // แสดง loading
            showLoadingState();

            // ไปหน้าวันนี้ (ไม่มี date parameter)
            const baseUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            const hashUrl = '#apps/sales_overview_bwd/index.php';
            const newUrl = baseUrl + hashUrl;

            window.location.href = newUrl;
        });

        // Auto-submit เมื่อเปลี่ยนวันที่
        $('#dateInput').on('change', function() {
            $('#dateFilterForm').submit();
        });

        // Helper function to validate date
        function isValidDate(dateString) {
            const regex = /^\d{4}-\d{2}-\d{2}$/;
            if (!regex.test(dateString)) {
                return false;
            }

            const date = new Date(dateString);
            const timestamp = date.getTime();

            if (typeof timestamp !== 'number' || Number.isNaN(timestamp)) {
                return false;
            }

            return date.toISOString().startsWith(dateString);
        }

        function showLoadingState() {
            $('.card').addClass('card-loading');
            $('#dateFilterForm button[type="submit"]').addClass('loading');

            if (typeof App !== 'undefined' && App.startLoading) {
                App.startLoading();
            }
        }

        // แสดง historical indicator หากไม่ใช่วันนี้
        const currentDate = $('#dateInput').val();
        const today = new Date().toISOString().split('T')[0];

        if (currentDate !== today) {
            updateHistoricalIndicator(currentDate);
        }

        function updateHistoricalIndicator(selectedDate) {
            const today = new Date().toISOString().split('T')[0];

            // ลบ indicator เก่า
            $('.historical-indicator').remove();

            if (selectedDate !== today) {
                const indicator = $('<div class="alert alert-info historical-indicator d-flex align-items-center mb-3">' +
                    '<i class="fas fa-info-circle mr-2"></i>' +
                    '<span class="flex-grow-1">กำลังดูข้อมูลวันที่ <strong>' + formatDateThai(selectedDate) + '</strong></span>' +
                    '<button type="button" class="btn btn-sm btn-outline-primary" id="quickToday">' +
                    '<i class="fas fa-calendar-day"></i> วันนี้' +
                    '</button>' +
                    '</div>');

                $('.row').before(indicator);

                // Event สำหรับปุ่ม quick today
                $('#quickToday').on('click', function() {
                    $('#btnToday').click();
                });
            }
        }

        // เพิ่ม status indicators ตาม content
        addStatusIndicators();

        function addStatusIndicators() {
            const cards = $('.card');
            cards.each(function(index) {
                const card = $(this);
                const cardHeader = card.find('.card-title').text().toLowerCase();

                if (cardHeader.includes('ภาพรวม') || cardHeader.includes('เลือกวันที่')) {
                    card.addClass('status-today');
                } else if (cardHeader.includes('จัดส่งวันนี้')) {
                    card.addClass('status-today');
                } else if (cardHeader.includes('อนาคต') || cardHeader.includes('ภาพรวมการจัดส่ง')) {
                    card.addClass('status-future');
                } else if (cardHeader.includes('ยอดขาย')) {
                    card.addClass('status-sales');
                } else if (cardHeader.includes('ลบ')) {
                    card.addClass('status-removed');
                } else if (cardHeader.includes('ขายคืน')) {
                    card.addClass('status-return');
                }
            });
        }

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            if ($(e.target).is('input, textarea, select')) {
                return;
            }

            // Ctrl+T สำหรับวันนี้
            if (e.ctrlKey && e.keyCode === 84) {
                e.preventDefault();
                $('#btnToday').click();
            }

            // Ctrl+D สำหรับ focus ที่ date input
            if (e.ctrlKey && e.keyCode === 68) {
                e.preventDefault();
                $('#dateInput').focus();
            }
        });

        // เพิ่ม tooltip สำหรับปุ่ม
        if (typeof $().tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        $('#btnToday').attr('title', 'กลับไปดูข้อมูลวันนี้ (Ctrl+T)');
        $('#dateInput').attr('title', 'เลือกวันที่ที่ต้องการดูข้อมูล (Ctrl+D)');

        function formatDateThai(dateString) {
            const date = new Date(dateString);
            const thaiMonths = [
                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];

            const day = date.getDate();
            const month = thaiMonths[date.getMonth()];
            const year = date.getFullYear() + 543;

            return `${day} ${month} ${year}`;
        }

        // Debug: แสดง URL ปัจจุบัน
        console.log('Current URL:', window.location.href);
        console.log('Current Hash:', window.location.hash);
    });
</script>