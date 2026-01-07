<style>
    :root {
        --primary-color: #00204E;
        --primary-light: #003d7a;
        --primary-dark: #001833;
        --white: #ffffff;
        --light-gray: #f8f9fa;
        --medium-gray: #e9ecef;
        --dark-gray: #6c757d;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #17a2b8;
        --shadow: rgba(0, 32, 78, 0.15);
    }

    body {
        background: linear-gradient(135deg, var(--light-gray) 0%, #e3f2fd 100%);
        min-height: 100vh;
    }

    .page-container {
        background: var(--white);
        border-radius: 20px;
        box-shadow: 0 15px 40px var(--shadow);
        margin: 20px auto;
        padding: 0;
        overflow: hidden;
        max-width: 1400px;
        position: relative;
    }

    .page-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light), var(--info), var(--primary-light), var(--primary-color));
        background-size: 200% 100%;
        animation: gradientShift 3s ease-in-out infinite;
    }

    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        padding: 25px 30px;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: shimmer 4s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes shimmer {

        0%,
        100% {
            transform: translateX(-100%) translateY(-100%) rotate(0deg);
        }

        50% {
            transform: translateX(0%) translateY(0%) rotate(180deg);
        }
    }

    .page-content {
        padding: 30px;
    }

    /* Back Button */
    .back-button {
        background: linear-gradient(135deg, var(--danger) 0%, #e74c3c 100%);
        border: none;
        color: var(--white);
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
    }

    .back-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        background: linear-gradient(135deg, #e74c3c 0%, var(--danger) 100%);
    }

    .back-button::before {
        content: '←';
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* Info Cards */
    .info-section {
        background: linear-gradient(135deg, var(--light-gray) 0%, var(--medium-gray) 100%);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 5px solid var(--primary-color);
        box-shadow: 0 8px 25px var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .info-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(0, 32, 78, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }

    .list-group-horizontal {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px var(--shadow);
        margin-bottom: 20px;
    }

    .list-group-item {
        border: none;
        background: var(--white);
        padding: 20px 15px;
        transition: all 0.3s ease;
        position: relative;
    }

    .list-group-item:hover {
        background: rgba(0, 32, 78, 0.05);
        transform: translateY(-2px);
    }

    .list-group-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 20%;
        bottom: 20%;
        width: 1px;
        background: linear-gradient(to bottom, transparent, var(--medium-gray), transparent);
    }

    .list-group-item .text-secondary {
        color: var(--primary-color) !important;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .list-group-item strong {
        color: #333;
        font-size: 1.1rem;
        font-weight: 700;
    }

    /* Form Controls */
    .form-control,
    .form-select {
        border: 2px solid var(--medium-gray);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--white);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 32, 78, 0.15);
        outline: none;
        transform: translateY(-1px);
    }

    .form-control:hover,
    .form-select:hover {
        border-color: var(--primary-color);
    }

    label {
        color: var(--primary-color);
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 8px;
        display: block;
    }

    /* Buttons */
    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.6s ease;
    }

    .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--warning) 0%, #ffb300 100%);
        color: #333;
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
    }

    .btn-warning:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
        background: linear-gradient(135deg, #ffb300 0%, var(--warning) 100%);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        box-shadow: 0 4px 15px var(--shadow);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px var(--shadow);
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--danger) 0%, #e74c3c 100%);
        color: var(--white);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        background: linear-gradient(135deg, #e74c3c 0%, var(--danger) 100%);
    }

    /* Form Section */
    .form-section {
        background: var(--white);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px var(--shadow);
        border: 1px solid var(--medium-gray);
    }

    .form-section-title {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section-title::before {
        content: '⚙️';
        font-size: 1.3rem;
    }

    /* Table Styling */
    .table-container {
        background: var(--white);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 25px var(--shadow);
        border: 1px solid var(--medium-gray);
        margin-bottom: 25px;
    }

    .table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        font-weight: 700;
        font-size: 0.9rem;
        padding: 15px 12px;
        border: none;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        position: relative;
    }

    .table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .table tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border-bottom: 1px solid var(--medium-gray);
        font-size: 0.9rem;
        color: #333;
        transition: all 0.3s ease;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: rgba(0, 32, 78, 0.05);
        transform: translateY(-1px);
        box-shadow: 0 5px 15px var(--shadow);
    }

    .table tbody tr:nth-child(even) {
        background: rgba(248, 249, 250, 0.5);
    }

    .table tbody tr:nth-child(even):hover {
        background: rgba(0, 32, 78, 0.05);
    }

    /* Summary Cards */
    .summary-section {
        margin-top: 30px;
    }

    .summary-card {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        border: 2px solid var(--medium-gray);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 25px var(--shadow);
        transition: all 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px var(--shadow);
    }

    .summary-card .list-group-item {
        background: transparent;
        border: none;
        padding: 20px;
    }

    .summary-card .list-group-item:first-child {
        background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        color: var(--white);
    }

    .summary-card .list-group-item:nth-child(2) {
        background: linear-gradient(135deg, var(--info) 0%, #138496 100%);
        color: var(--white);
    }

    .summary-card .list-group-item:last-child {
        background: linear-gradient(135deg, var(--warning) 0%, #ffb300 100%);
        color: #333;
    }

    .summary-card strong {
        font-size: 1.3rem !important;
        font-weight: 800 !important;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    /* Animation */
    .fade-in {
        animation: fadeIn 0.8s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-in {
        animation: slideIn 0.6s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-container {
            margin: 10px;
            border-radius: 15px;
        }

        .page-content {
            padding: 20px;
        }

        .list-group-horizontal {
            flex-direction: column;
        }

        .list-group-item:not(:last-child)::after {
            display: none;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .table-responsive {
            border-radius: 10px;
        }
    }

    /* Loading States */
    .loading {
        opacity: 0.6;
        pointer-events: none;
        position: relative;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        border: 2px solid var(--primary-color);
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }
</style>
</head>

<body>
    <div class="page-container fade-in">
        <div class="page-header">
            <h1 style="margin: 0; font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-boxes"></i>
                รายละเอียดการจัดส่งสินค้า
            </h1>
        </div>

        <div class="page-content">
            <!-- Back Button -->
            <div class="col-12 slide-in">
                <button onclick="window.history.back();" class="back-button">
                    กลับ
                </button>
            </div>

            <?php
            global $os;
            $iform = new iform($dbc, $this->auth);
            $delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $_GET['id']);
            $order = $dbc->GetRecord("bs_orders_bwd", "*", "delivery_id=" . $delivery['id']);

            $pack = $dbc->GetRecord("bs_bwd_pack_items", "SUM(item_type) AS aa", "delivery_id=" . $delivery['id']);

            if ($delivery['status'] == 1) {
                $edit_mode = 'style="display:none;"';
                $edit_mode1 = 'style="display:block;"';
            } else {
                $edit_mode = 'style="display:block;"';
                $edit_mode1 = 'style="display:none;"';
            }
            ?>

            <!-- Hidden Table for Data -->
            <table id="tblItemDetail" data-id="<?php echo $delivery['id']; ?>" class="table table-bordered table-sm mt-2" style="display: none;"></table>

            <!-- Delivery Information -->
            <div class="info-section slide-in">
                <ul class="list-group list-group-horizontal mb-0">
                    <li class="list-group-item flex-fill text-center">
                        <div class="text-secondary">ประเภท</div>
                        <strong>
                            <?php
                            if ($delivery['type'] == 1) {
                                echo '<span style="color: #28a745;">แบบธรรมดา</span>';
                            } else {
                                echo '<span style="color: #17a2b8;">แบบรวม</span>';
                            }
                            ?>
                        </strong>
                    </li>
                    <li class="list-group-item flex-fill text-center">
                        <div class="text-secondary">คำสั่งซื้อ</div>
                        <strong>
                            <?php
                            if ($delivery['type'] == 2) {
                                $sql = "SELECT * FROM bs_orders_bwd WHERE delivery_id=" . $order['delivery_id'];
                                $rst = $dbc->Query($sql);
                                while ($item = $dbc->Fetch($rst)) {
                                    echo '<div style="color: #00204E;">' . $item['code'] . '</div>';
                                }
                            } else {
                                echo '<span style="color: #00204E;">' . $order['code'] . '</span>';
                            }
                            ?>
                        </strong>
                    </li>
                    <li class="list-group-item flex-fill text-center">
                        <div class="text-secondary">วันที่สั่งซื้อ</div>
                        <strong style="color: #6c757d;"><?php echo $order['created']; ?></strong>
                    </li>
                    <li class="list-group-item flex-fill text-center">
                        <div class="text-secondary">วันที่ส่ง</div>
                        <strong style="color: #dc3545;"><?php echo $delivery['delivery_date']; ?></strong>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="form-section slide-in">
                <div class="form-section-title">การดำเนินการ</div>
                <div class="action-buttons">
                    <input type="hidden" name="id" value="<?php echo $delivery['id']; ?>">
                    <button onclick="fn.app.prepare_bwd.delivery.calculate()" class="btn btn-warning" <?php echo $edit_mode; ?>>
                        <i class="fas fa-check-circle"></i>
                        SUBMIT
                    </button>
                    <button onclick="fn.app.prepare_bwd.delivery.calcu()" class="btn btn-warning" <?php echo $edit_mode1; ?>>
                        <i class="fas fa-undo"></i>
                        UNSUBMIT
                    </button>
                </div>
            </div>

            <!-- Add Item Form -->
            <div class="form-section slide-in">
                <div class="form-section-title">เพิ่มรายการแท่งเงิน</div>
                <div class="row">
                    <div class="col-md-6">
                        <label>หมายเลขแท่ง</label>
                        <select name="code_search" type="text" class="form-control" placeholder="เลือกรายการสินค้า">
                            <option value="">-- เลือกหมายเลขแท่ง --</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button onclick="fn.app.prepare_bwd.delivery.mapping()" class="btn btn-primary" <?php echo $edit_mode; ?>>
                            <i class="fas fa-plus-circle"></i>
                            เพิ่มรายการ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Silver Detail Table -->
            <div class="table-container slide-in">
                <div class="table-responsive">
                    <table id="tblSilverDetail" class="table table-form table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">หมายเลขแท่ง</th>
                                <th class="text-center">ประเภท</th>
                                <th class="text-center">รายการ</th>
                                <th class="text-center">น้ำหนัก</th>
                                <th class="text-center">จำนวนแท่ง</th>
                                <th class="text-center">ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="summary-section slide-in">
                <div class="summary-card">
                    <ul class="list-group list-group-horizontal">
                        <li class="list-group-item flex-fill text-center">
                            <div class="text-secondary">Order Bars</div>
                            <strong><?php echo number_format($delivery['amount'], 4); ?></strong>
                        </li>
                        <li class="list-group-item flex-fill text-center">
                            <div class="text-secondary">Packing</div>
                            <strong id="amount_total" data-id="<?php echo $pack['aa']; ?>">0.00</strong>
                        </li>
                        <li class="list-group-item flex-fill text-center">
                            <div class="text-secondary">Remain</div>
                            <strong id="amount_remain">0.00</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // เพิ่มเอฟเฟกต์ loading เมื่อคลิกปุ่ม
            $('.btn').on('click', function() {
                var $this = $(this);
                $this.addClass('loading');

                setTimeout(function() {
                    $this.removeClass('loading');
                }, 2000);
            });

            // เพิ่มเอฟเฟกต์เมื่อ focus form control
            $('.form-control, select').on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });

            // Animation delay สำหรับ slide-in elements
            $('.slide-in').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            // Hover effect สำหรับ summary cards
            $('.summary-card .list-group-item').hover(
                function() {
                    $(this).css('transform', 'scale(1.02)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                }
            );
        });
    </script>