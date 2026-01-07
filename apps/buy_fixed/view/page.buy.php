<?php
global $ui_form, $os;

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");
?>

<style>
    /* Base styles */
    .form-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 24px;
        margin-bottom: 20px;
    }

    .form-title {
        font-size: 18px;
        font-weight: 600;
        color: #00204E;
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid #E8F1F8;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 500;
        color: #5a6c7d;
        margin-bottom: 6px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e1e8ed;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #ffffff;
        min-height: 48px;
    }

    .form-input:focus {
        outline: none;
        border-color: #00204E;
        box-shadow: 0 0 0 3px rgba(0, 32, 78, 0.1);
        background: #fafbfc;
    }

    .file-input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .file-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-input-display {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px 12px;
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 48px;
    }

    .file-input-display:hover {
        border-color: #00204E;
        background: #E8F1F8;
        color: #00204E;
    }

    .file-input-display i {
        margin-right: 8px;
    }

    .help-text {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .submit-btn {
        background: linear-gradient(135deg, #00204E 0%, #003366 100%);
        color: white;
        border: none;
        padding: 16px 24px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        min-height: 56px;
        /* เพิ่มความสูงสำหรับมือถือ */
    }

    .submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 32, 78, 0.4);
    }

    .table-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 20px;
        /* เพิ่ม margin bottom สำหรับการแยกตาราง */
    }

    .table-header {
        background: linear-gradient(135deg, #00204E 0%, #003366 100%);
        color: white;
        padding: 16px 20px;
        font-size: 16px;
        font-weight: 600;
    }

    /* Table responsive wrapper */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #tblPurchase {
        margin: 0;
        border: none;
        width: 100% !important;
        min-width: 800px;
        /* ขนาดขั้นต่ำสำหรับ table */
    }

    #tblBuyWeChat {
        margin: 0;
        border: none;
        width: 100% !important;
        min-width: 800px;
    }



    #tblPurchase thead th {
        background: #F8FAFC !important;
        color: #00204E !important;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 8px !important;
        border: none !important;
        border-bottom: 2px solid #00204E !important;
        white-space: nowrap;
    }

    #tblBuyWeChat thead th {
        background: #F8FAFC !important;
        color: #00204E !important;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 8px !important;
        border: none !important;
        border-bottom: 2px solid #00204E !important;
        white-space: nowrap;
    }

    #tblPurchase tbody tr:hover {
        background: #E8F1F8 !important;
    }

    #tblBuyWeChat tbody tr:hover {
        background: #E8F1F8 !important;
    }

    #tblPurchase tbody td {
        padding: 12px 8px !important;
        border-top: 1px solid #e2e8f0 !important;
        font-size: 14px;
        white-space: nowrap;
    }

    #tblBuyWeChat tbody td {
        padding: 12px 8px !important;
        border-top: 1px solid #e2e8f0 !important;
        font-size: 14px;
        white-space: nowrap;
    }

    /* DataTable wrapper styles */
    .dataTables_wrapper {
        padding: 15px;
    }

    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        margin: 10px 0;
    }

    .dataTables_length select,
    .dataTables_filter input {
        border: 1.5px solid #e1e8ed;
        border-radius: 6px;
        padding: 8px 12px;
        margin: 0 5px;
        font-size: 14px;
        min-height: 40px;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: #00204E;
        box-shadow: 0 0 0 2px rgba(0, 32, 78, 0.1);
    }

    .dataTables_paginate .paginate_button {
        padding: 8px 12px !important;
        margin: 0 2px !important;
        border-radius: 6px !important;
        border: 1px solid #e1e8ed !important;
        background: white !important;
        color: #00204E !important;
        min-height: 40px !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: #00204E !important;
        color: white !important;
        border-color: #00204E !important;
    }

    .dataTables_paginate .paginate_button.current {
        background: #00204E !important;
        color: white !important;
        border-color: #00204E !important;
    }

    /* Highlight สำหรับแถวที่ยังไม่ได้แนบรูป */
    #tblPurchase tbody tr.missing-image {
        background: linear-gradient(135deg, #FEF7E6 0%, #FFF4E6 100%) !important;
        border-left: 4px solid #F59E0B !important;
        box-shadow: inset 0 1px 3px rgba(245, 158, 11, 0.1) !important;
    }

    #tblBuyWeChat tbody tr.missing-image {
        background: linear-gradient(135deg, #FEF7E6 0%, #FFF4E6 100%) !important;
        border-left: 4px solid #F59E0B !important;
        box-shadow: inset 0 1px 3px rgba(245, 158, 11, 0.1) !important;
    }

    #tblPurchase tbody tr.missing-image:hover {
        background: linear-gradient(135deg, #FED7AA 0%, #FDBA74 100%) !important;
        transform: translateX(2px);
        transition: all 0.3s ease;
    }

    #tblBuyWeChat tbody tr.missing-image:hover {
        background: linear-gradient(135deg, #FED7AA 0%, #FDBA74 100%) !important;
        transform: translateX(2px);
        transition: all 0.3s ease;
    }

    #tblPurchase tbody tr.missing-image td {
        border-top: 1px solid #F59E0B !important;
    }

    #tblBuyWeChat tbody tr.missing-image td {
        border-top: 1px solid #F59E0B !important;
    }

    /* ปุ่มแนบรูปที่ปรับแต่งให้เข้ากับ theme */
    .upload-btn-missing {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%) !important;
        color: white !important;
        border: none !important;
        padding: 10px 16px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3) !important;
        min-height: 40px !important;
    }

    .upload-btn-missing:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4) !important;
    }

    .upload-btn-missing i {
        margin-right: 6px !important;
    }

    /* Animation สำหรับการเตือน */
    .missing-image-pulse {
        animation: gentle-pulse 3s infinite ease-in-out;
    }

    @keyframes gentle-pulse {
        0% {
            background: linear-gradient(135deg, #FEF7E6 0%, #FFF4E6 100%);
            box-shadow: inset 0 1px 3px rgba(245, 158, 11, 0.1);
        }

        50% {
            background: linear-gradient(135deg, #FED7AA 0%, #FDBA74 100%);
            box-shadow: inset 0 1px 3px rgba(245, 158, 11, 0.2);
        }

        100% {
            background: linear-gradient(135deg, #FEF7E6 0%, #FFF4E6 100%);
            box-shadow: inset 0 1px 3px rgba(245, 158, 11, 0.1);
        }
    }

    /* Tooltip สำหรับบอกว่าต้องแนบรูป */
    .missing-image-tooltip {
        position: relative;
    }

    .missing-image-tooltip::before {
        content: "ต้องแนบรูปภาพ";
        position: absolute;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: #00204E;
        color: white;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 11px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 1000;
    }

    .missing-image-tooltip::after {
        content: "";
        position: absolute;
        top: -8px;
        left: 50%;
        transform: translateX(-50%);
        border: 4px solid transparent;
        border-top-color: #00204E;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .missing-image-tooltip:hover::before,
    .missing-image-tooltip:hover::after {
        opacity: 1;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .form-container {
            padding: 16px;
            margin-bottom: 15px;
        }

        .form-title {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-label {
            font-size: 12px;
            margin-bottom: 4px;
        }

        .form-input {
            padding: 14px 16px;
            font-size: 16px;
            /* ป้องกัน zoom บน iOS */
            min-height: 50px;
        }

        .submit-btn {
            padding: 18px 24px;
            font-size: 16px;
            min-height: 58px;
        }

        .table-header {
            padding: 12px 16px;
            font-size: 14px;
        }

        .table-container {
            margin-bottom: 15px;
        }

        .dataTables_wrapper {
            padding: 10px;
        }

        #tblPurchase thead th {
            font-size: 11px;
            padding: 10px 6px !important;
        }

        #tblBuyWeChat thead th {
            font-size: 11px;
            padding: 10px 6px !important;
        }

        #tblPurchase tbody td {
            padding: 10px 6px !important;
            font-size: 13px;
        }

        #tblBuyWeChat tbody td {
            padding: 10px 6px !important;
            font-size: 13px;
        }

        .upload-btn-missing {
            padding: 8px 12px !important;
            font-size: 11px !important;
            min-height: 36px !important;
        }

        /* ปรับ DataTable controls สำหรับมือถือ */
        .dataTables_length,
        .dataTables_filter {
            text-align: center;
            margin-bottom: 15px;
        }

        .dataTables_length select,
        .dataTables_filter input {
            width: 100%;
            max-width: 200px;
            margin: 5px 0;
        }

        .dataTables_info {
            text-align: center;
            font-size: 12px;
            margin: 10px 0;
        }

        .dataTables_paginate {
            text-align: center;
        }

        .dataTables_paginate .paginate_button {
            padding: 10px 8px !important;
            margin: 2px !important;
            font-size: 12px !important;
        }
    }

    /* Extra small mobile */
    @media (max-width: 480px) {
        .form-container {
            padding: 12px;
            border-radius: 8px;
        }

        .form-title {
            font-size: 15px;
            text-align: center;
        }

        .table-header {
            text-align: center;
            font-size: 13px;
        }

        #tblPurchase {
            min-width: 600px;
            /* ลดขนาดขั้นต่ำสำหรับมือถือเล็ก */
        }

        #tblBuyWeChat {
            min-width: 600px;
            /* ลดขนาดขั้นต่ำสำหรับมือถือเล็ก */
        }

        #tblPurchase thead th {
            font-size: 10px;
            padding: 8px 4px !important;
        }

        #tblBuyWeChat thead th {
            font-size: 10px;
            padding: 8px 4px !important;
        }

        #tblPurchase tbody td {
            padding: 8px 4px !important;
            font-size: 12px;
        }

        #tblBuyWeChat tbody td {
            padding: 8px 4px !important;
            font-size: 12px;
        }

        .upload-btn-missing {
            padding: 6px 10px !important;
            font-size: 10px !important;
            min-height: 32px !important;
        }

        .dataTables_paginate .paginate_button {
            padding: 8px 6px !important;
            font-size: 11px !important;
        }
    }

    /* Tablet landscape */
    @media (min-width: 769px) and (max-width: 1024px) {
        .form-container {
            padding: 20px;
        }

        #tblPurchase thead th {
            font-size: 13px;
            padding: 10px !important;
        }

        #tblBuyWeChat thead th {
            font-size: 13px;
            padding: 10px !important;
        }

        #tblPurchase tbody td {
            padding: 10px !important;
            font-size: 13px;
        }

        #tblBuyWeChat tbody td {
            padding: 10px !important;
            font-size: 13px;
        }
    }

    /* Touch-friendly improvements */
    @media (pointer: coarse) {
        .form-input {
            min-height: 50px;
            font-size: 16px;
        }

        .submit-btn {
            min-height: 56px;
            padding: 18px 24px;
        }

        .upload-btn-missing {
            min-height: 44px !important;
            padding: 12px 16px !important;
        }

        .dataTables_paginate .paginate_button {
            min-height: 44px !important;
            padding: 12px 16px !important;
        }
    }
</style>

<div class="row">
    <div class="col-12 col-lg-3 order-2 order-lg-1">
        <div class="form-container">
            <div class="form-title">
                <i class="fas fa-plus-circle" style="margin-right: 8px; color: #00204E;"></i>
                เพิ่มรายการซื้อ
            </div>

            <form name="form_addusd" onsubmit="fn.app.buy_fixed.buy.add();return false;">
                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <?php
                    $ui_form->EchoItem(array(
                        "name" => "supplier_id",
                        "type" => "comboboxdb",
                        "class" => "form-input",
                        "source" => array(
                            "table" => "bs_suppliers",
                            "name" => "name",
                            "value" => "id",
                            "where" => "status = 1"
                        )
                    ));
                    ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <?php
                    $ui_form->EchoItem(array(
                        "name" => "type",
                        "type" => "combobox",
                        "class" => "form-input",
                        "source" => array(
                            "Buy",
                            "Sell",
                        )
                    ));
                    ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount</label>
                    <?php
                    $ui_form->EchoItem(array(
                        "name" => "amount",
                        "class" => "form-input text-right",
                        "placeholder" => "0.00"
                    ));
                    ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <?php
                    $ui_form->EchoItem(array(
                        "type" => "date",
                        "name" => "date",
                        "class" => "form-input",
                        "value" => date("Y-m-d")
                    ));
                    ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Maturity</label>
                    <?php
                    $ui_form->EchoItem(array(
                        "name" => "method",
                        "type" => "combobox",
                        "class" => "form-input",
                        "source" => array(
                            "Today",
                            "Forward",
                            "TOM",
                            "SPOT",
                            "1D",
                            "1W",
                            "1M",
                            "2M",
                            "3M",
                        )
                    ));
                    ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Product</label>
                    <?php
                    $ui_form->EchoItem(array(
                        "name" => "product_id",
                        "type" => "comboboxdb",
                        "class" => "form-input",
                        "source" => array(
                            "table" => "bs_products",
                            "name" => "name",
                            "value" => "id"
                        )
                    ));
                    ?>
                </div>

                <button class="submit-btn" type="submit">
                    <i class="fas fa-check" style="margin-right: 6px;"></i>
                    ทำรายการ
                </button>
            </form>
        </div>
    </div>

    <div class="col-12 col-lg-9 order-1 order-lg-2">
        <div class="table-container">
            <div class="table-header">
                <i class="fas fa-list" style="margin-right: 8px;"></i>
                Buy / Sell ICBC
            </div>
            <div class="table-responsive">
                <table id="tblPurchase" class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Confirm</th>
                            <th class="text-center">Purchase Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Ounces</th>
                            <th class="text-center">Maturity</th>
                            <th class="text-center">รูปภาพ</th>
                            <th class="text-center">User</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <i class="fas fa-list" style="margin-right: 8px;"></i>
                Buy / Sell WeChat
            </div>
            <div class="table-responsive">
                <table id="tblBuyWeChat" class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Confirm</th>
                            <th class="text-center">Purchase Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Ounces</th>
                            <th class="text-center">Maturity</th>
                            <th class="text-center">รูปภาพ</th>
                            <th class="text-center">User</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>