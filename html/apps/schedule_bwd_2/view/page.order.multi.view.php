<?php
$today = time();
$main_order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($main_order_id <= 0) {
    die("Invalid order ID");
}

$main_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $main_order_id);
if (!$main_order) {
    die("Order not found");
}

$all_orders = [];
$all_orders_query = "SELECT * FROM bs_orders_bwd WHERE (id = $main_order_id OR parent = $main_order_id) ORDER BY id ASC";
$all_orders_result = $dbc->query($all_orders_query);
if ($all_orders_result) {
    while ($row = mysqli_fetch_assoc($all_orders_result)) {
        $all_orders[] = $row;
    }
}

$delivery = null;
if (!empty($main_order['delivery_id'])) {
    $delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $main_order['delivery_id']);
}

$shipping_name = '-';
if (!empty($main_order['shipping'])) {
    $shipping = $dbc->GetRecord("bs_shipping_bwd", "*", "id=" . $main_order['shipping']);
    if ($shipping) {
        $shipping_name = $shipping['name'];
    }
}

$sales = "-";
$signature = "";
if (!empty($main_order['sales']) && $dbc->HasRecord("os_users", "id=" . $main_order['sales'])) {
    $employee = $dbc->GetRecord("os_users", "*", "id=" . $main_order['sales']);
    $sales = $employee['display'];
    $signature = $employee['name'];
}

$cus = "-";
if (!empty($main_order['customer_id']) && $dbc->HasRecord("bs_customers_bwd", "id=" . $main_order['customer_id'])) {
    $customers = $dbc->GetRecord("bs_customers_bwd", "*", "id=" . $main_order['customer_id']);
    $cus = $customers['username'];
}

if ($delivery) {
    if (empty($delivery['payment_note'])) {
        $payment_note = array(
            "bank" => $delivery['default_bank'] ?? '',
            "payment" => $delivery['default_payment'] ?? '',
            "remark" => ""
        );
    } else {
        $payment_note = json_decode($delivery['payment_note'], true);
        if (!is_array($payment_note)) {
            $payment_note = array("bank" => "", "payment" => "", "remark" => "");
        }
    }
} else {
    $payment_note = array("bank" => "", "payment" => "", "remark" => "");
}


$grand_total = 0;
$total_bars = 0;
foreach ($all_orders as $order) {
    $grand_total += floatval($order['net']);
    $total_bars += floatval($order['amount']);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบยืนยันการสั่งซื้อ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.css" rel="stylesheet">
    <style>
        .small-text {
            font-size: 11.2pt;
        }

        .big-text {
            font-size: 16pt;
        }

        .under-line {
            border-bottom: 1px solid #000;
        }

        .flower {
            border: 2px solid black;
            padding: 10px;
        }

        .order-item {
            background-color: #f9f9f9;
            margin-bottom: 15px;
        }

        .product-img {
            border-radius: 5px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 8px;
            margin-top: 0;
            flex-shrink: 0;
        }

        .checkbox-item label {
            margin-bottom: 0;
            font-size: 11pt;
        }

        /* Print Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }

            body {
                font-size: 12pt;
                line-height: 1.3;
                color: #000 !important;
                background: white !important;
            }

            .main-header,
            .sidebar,
            .breadcrumb,
            .btn-area,
            button {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card-body {
                margin: 0 !important;
                padding: 0 !important;
            }

            .container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .order-item {
                page-break-inside: avoid;
                background-color: #f5f5f5 !important;
                border: 1px solid #ddd !important;
                margin-bottom: 10px !important;
                padding: 10px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .flower {
                border: 2px solid black !important;
                padding: 8px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .under-line {
                border-bottom: 1px solid #000 !important;
            }

            h4,
            h5 {
                margin: 10px 0 !important;
                page-break-after: avoid;
            }

            .row {
                margin: 0 !important;
            }

            .col-1,
            .col-2,
            .col-3,
            .col-4,
            .col-5,
            .col-6,
            .col-7,
            .col-8,
            .col-9,
            .col-10,
            .col-11,
            .col-12,
            .col-md-1,
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-5,
            .col-md-6,
            .col-md-7,
            .col-md-8,
            .col-md-9,
            .col-md-10,
            .col-md-11,
            .col-md-12 {
                padding: 2px !important;
            }

            .order-item {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            table,
            .signature-section {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .keep-together {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            img {
                max-width: 100% !important;
                height: auto !important;
            }

            .product-img {
                width: 50px !important;
                height: 50px !important;
            }

            .signature-table {
                font-size: 9pt !important;
                width: 100% !important;
            }

            .signature-table td {
                padding: 3px !important;
                font-size: 8pt !important;
            }

            .order-item {
                margin-bottom: 8px !important;
                padding: 8px !important;
            }

            .order-item .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }

            .order-item .col-md-2 {
                flex: 0 0 15% !important;
                max-width: 15% !important;
            }

            .order-item .col-md-5 {
                flex: 0 0 42% !important;
                max-width: 42% !important;
            }

            .order-item .col-md-3 {
                flex: 0 0 28% !important;
                max-width: 28% !important;
            }

            .order-item .col-md-2:last-child {
                flex: 0 0 15% !important;
                max-width: 15% !important;
            }

            .checkbox-item {
                display: flex !important;
                align-items: center !important;
                margin-bottom: 6px !important;
                line-height: 1.1 !important;
            }

            .checkbox-item input[type="checkbox"] {
                width: 14px !important;
                height: 14px !important;
                margin-right: 8px !important;
                margin-top: 0 !important;
                flex-shrink: 0 !important;
                -webkit-appearance: none !important;
                -moz-appearance: none !important;
                appearance: none !important;
                border: 2px solid #000 !important;
                background: white !important;
                outline: none !important;
                box-shadow: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                position: relative !important;
                display: inline-block !important;
            }

            /* สร้างกล่อง checkbox ที่เห็นชัด */
            .checkbox-item input[type="checkbox"]::before {
                content: "" !important;
                position: absolute !important;
                top: -2px !important;
                left: -2px !important;
                width: 14px !important;
                height: 14px !important;
                border: 2px solid #000 !important;
                background: white !important;
                display: block !important;
            }

            .checkbox-item input[type="checkbox"]:checked::after {
                content: "✓" !important;
                position: absolute !important;
                top: -1px !important;
                left: 1px !important;
                font-size: 12px !important;
                font-weight: bold !important;
                color: #000 !important;
                z-index: 1 !important;
            }

            .checkbox-item label {
                margin-bottom: 0 !important;
                font-size: 10pt !important;
                line-height: 1.1 !important;
                color: #000 !important;
            }

            input[type="checkbox"]:focus,
            input[type="checkbox"]:active {
                outline: none !important;
                box-shadow: none !important;
                border: 2px solid #000 !important;
            }

            strong {
                font-weight: bold !important;
            }

            * {
                background: transparent !important;
            }

            .order-item {
                background: #f9f9f9 !important;
            }

            a[href]:after {
                content: none !important;
                display: none !important;
            }

            a {
                color: inherit !important;
                text-decoration: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 1cm;

                @top-left {
                    content: none;
                }

                @top-center {
                    content: none;
                }

                @top-right {
                    content: none;
                }

                @bottom-left {
                    content: none;
                }

                @bottom-center {
                    content: none;
                }

                @bottom-right {
                    content: none;
                }

                margin-header: 0;
                margin-footer: 0;
            }

            [href] {
                color: inherit !important;
            }

            [href]:after {
                content: "" !important;
                display: none !important;
            }
        }

        @media print and (-webkit-min-device-pixel-ratio: 0) {
            .order-item {
                -webkit-print-color-adjust: exact;
            }

            .flower {
                -webkit-print-color-adjust: exact;
            }

            .checkbox-item input[type="checkbox"] {
                -webkit-print-color-adjust: exact !important;
            }

            @media print {
                @page {
                    size: A4 portrait;
                    margin: 0;
                }

                body {
                    margin: 1cm;
                }

            }

        }
    </style>
</head>

<body>

    <div class="btn-area btn-group mb-2">
        <button type="button" class="btn btn-dark" onclick='window.history.back()'>Back</button>
        <button class="btn btn-light has-icon mt-1 mt-sm-0" type="button" onclick="window.print()">
            <i class="mr-2" data-feather="printer"></i>Print ใบยืนยันการสั่งซื้อ
        </button>
    </div>

    <div class="card">
        <div class="card-body mr-4 ml-4"></div>
        <div class="d-flex align-items-center container">
            <div>
                <img class="pull-right" src="img/logo-Bowins-design.png" width="120px" height="120px">
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end" class="container">
            <div>FM-SM-006</div>
        </div>

        <div class="container docu-print mt-2">
            <h4 class="text-center">ใบยืนยันการสั่งซื้อ / CONFIRM ORDER</h4>
            <br>

            <!-- ข้อมูลลูกค้าและออเดอร์หลัก -->
            <div class="row small-text">
                <div class="col-sm-7">
                    <dl class="row col-8">
                        <dt class="col-5">Platform :</dt>
                        <dd class="col-6 under-line"><?php echo $main_order['platform']; ?></dd>
                        <dt class="col-4">ชื่อลูกค้า : </dt>
                        <dd class="col-7 under-line"><?php echo $main_order['customer_name']; ?></dd>
                        <dt class="col-5">Username :</dt>
                        <dd class="col-4 under-line"><?php echo $cus; ?></dd>
                        <dt class="col-5">โทร : </dt>
                        <dd class="col-6 under-line"><?php echo $main_order['phone']; ?></dd>

                    </dl>
                </div>
                <div class="col-sm-5">
                    <dl class="row col-8 offset-8">
                        <dt class="col-4">วันที่ :</dt>
                        <dd class="col-7 under-line"><?php echo date("d/m/Y", strtotime($main_order['date'])); ?></dd>
                        <dt class="col-4">No :</dt>
                        <dd class="col-8 under-line"><?php echo $main_order['code']; ?></dd>
                    </dl>
                </div>
            </div>

            <!-- ข้อมูลผู้ขายและการชำระเงิน -->
            <div class="row small-text">
                <div class="col-sm-12">
                    <dl class="row col-8">
                        <dt class="col-3">ผู้ขาย :</dt>
                        <dd class="col-4 under-line" style="font-family: cursive;"><?php echo $signature; ?></dd>
                        <dt class="col-3">การชำระเงิน :</dt>
                        <dd class="col-2 under-line"><?php echo $payment_note['bank']; ?></dd>

                        <dt class="col-3">เงื่อนไขการชำระเงิน :</dt>
                        <dd class="col-4 under-line"><?php echo $payment_note['payment']; ?></dd>
                        <dt class="col-3">หมายเหตุ :</dt>
                        <dd class="col-2 under-line"><?php echo $payment_note['remark']; ?></dd>
                    </dl>
                </div>
            </div>

            <!-- ที่อยู่ -->
            <div class="row small-text flower">
                <div class="col-12">
                    <dl class="row col-10 mt-2">
                        <dt class="col-3">ที่อยู่ออกอินวอยซ์ :</dt>
                        <dd class="col-8 under-line"><?php echo $main_order['billing_address']; ?></dd>
                        <dt class="col-3">ที่อยู่จัดส่ง :</dt>
                        <dd class="col-8 under-line"><?php echo $main_order['shipping_address']; ?></dd>
                    </dl>
                </div>
            </div>

            <!-- รายการสินค้าทั้งหมด -->
            <div class="row small-text flower mt-3 order-items-section">
                <div class="col-12">
                    <h5 class="text-center mb-3">รายการสินค้า (<?php echo count($all_orders); ?> รายการ)</h5>

                    <?php foreach ($all_orders as $index => $order): ?>
                        <?php
                        $product = $dbc->GetRecord("bs_products_bwd", "*", "id=" . $order['product_id']);
                        $product_name = $product ? $product['name'] : '-';

                        $product_type = $dbc->GetRecord("bs_products_type", "*", "id=" . $order['product_type']);
                        $product_type_name = $product_type ? $product_type['name'] : '-';

                        $ship = 0;
                        if (isset($order['shipping'])) {
                            if ($order['shipping'] == "1") {
                                $ship = 50;
                            } else if ($order['shipping'] == "2") {
                                $ship = 100;
                            } else if ($order['shipping'] == "3") {
                                $ship = 150;
                            } else if ($order['shipping'] == "4") {
                                $ship = 0;
                            }
                        }

                        $engrave_fee = 0;
                        if ($order['engrave'] == "สลักข้อความบนแท่งเงิน") {

                            $engrave_fee = 200 * $order['amount'];
                        } else {
                            $engrave_fee = 0;
                        }

                        $ai = 0;
                        if ($order['ai'] == "1") {

                            $ai = 300 * $order['amount']; 
                        } else {
                            $ai = 0; 
                        }

                        $img = '';
                        if ($order['product_id'] == 1) {
                            $img = '<img class="product-img" src="img/design-15.png" width="80px" height="80px">';
                            $aa = "แท่ง";
                        } else if ($order['product_id'] == 2) {
                            $img = '<img class="product-img" src="img/design-50.png" width="80px" height="80px">';
                            $aa = "แท่ง";
                        } else if ($order['product_id'] == 3) {
                            $img = '<img class="product-img" src="img/design-150.png" width="80px" height="80px">';
                            $aa = "แท่ง";
                        } else {
                            $aa = "กล่อง";
                        }
                        ?>

                        <div class="order-item mb-4 p-3" style="border: 1px solid #ddd; border-radius: 5px;">
                            <div class="row">
                                <div class="col-md-2 text-center">
                                    <?php echo $img; ?>
                                    <div class="mt-2"><strong>รายการที่ <?php echo $index + 1; ?></strong></div>
                                </div>
                                <div class="col-md-5">
                                    <dl class="row">
                                        <dt class="col-5">ขนาดแท่ง :</dt>
                                        <dd class="col-7"><?php echo $product_name; ?></dd>
                                        <dt class="col-5">ลาย :</dt>
                                        <dd class="col-7"><?php echo $product_type_name; ?></dd>
                                        <dt class="col-5">จำนวน :</dt>
                                        <dd class="col-7"><?php echo $order['amount']; ?> <?php echo $aa; ?></dd>
                                        <dt class="col-5">การสลักข้อความ :</dt>
                                        <dd class="col-7"><?php echo $order['engrave']; ?></dd>
                                    </dl>
                                </div>
                                <div class="col-md-3">
                                    <dl class="row">
                                        <dt class="col-6">Font :</dt>
                                        <dd class="col-6"><?php echo $order['font']; ?></dd>
                                        <dt class="col-6">Text :</dt>
                                        <dd class="col-6"><?php echo $order['carving']; ?></dd>
                                        <dt class="col-6">LASER เพิ่ม :</dt>
                                        <dd class="col-6"><?php echo ($order['ai'] == '1') ? 'ใช่' : 'ไม่'; ?></dd>
                                    </dl>
                                </div>
                                <div class="col-md-2">
                                    <dl class="row">
                                        <dt class="col-12 text-right">ราคาต่อแท่ง :</dt>
                                        <dd class="col-12 text-right"><?php echo number_format($order['price'], 2); ?> บาท</dd>
                                        <dt class="col-12 text-right">ส่วนลด :</dt>
                                        <dd class="col-12 text-right"><?php echo number_format($order['discount'], 2); ?> บาท</dd>
                                        <dt class="col-12 text-right">ค่่าส่ง :</dt>
                                        <dd class="col-12 text-right"><?php echo number_format($ship, 2); ?> บาท</dd>
                                        <dt class="col-12 text-right">ค่าสลัก :</dt>
                                        <dd class="col-12 text-right"><?php echo number_format($engrave_fee, 2); ?> บาท</dd>
                                        <dt class="col-12 text-right">ค่าเลเซอร์ :</dt>
                                        <dd class="col-12 text-right"><?php echo number_format($ai, 2); ?> บาท</dd>
                                        <dt class="col-12 text-right"><strong>รวม :</strong></dt>
                                        <dd class="col-12 text-right"><strong><?php echo number_format($order['net'], 2); ?> บาท</strong></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- สรุปยอดรวม -->
            <div class="row small-text flower mt-3 summary-section keep-together">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-12">อื่นๆภายในบรรจุภัณฑ์/พัสดุ</dt>
                        <dd class="col-12">
                            <div class="row">
                                <div class="col-6">
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check1">
                                        <label class="form-check-label" for="check1">แท่งเปล่า</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check2">
                                        <label class="form-check-label" for="check2">ซองกันกระแทก</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check3">
                                        <label class="form-check-label" for="check3">ซองพลาสติก</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check4">
                                        <label class="form-check-label" for="check4">Certificate Card</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check5">
                                        <label class="form-check-label" for="check5">Care Card</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check6">
                                        <label class="form-check-label" for="check6">About Artist Card</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check7">
                                        <label class="form-check-label" for="check7">ผ้าเช็ดแท่งเงิน</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check8">
                                        <label class="form-check-label" for="check8">ถุงใหญ่</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input class="form-check-input" type="checkbox" id="check9">
                                        <label class="form-check-label" for="check9">ถุงเล็ก</label>
                                    </div>
                                </div>
                            </div>
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-12">สรุปการสั่งซื้อ</dt>
                        <dd class="col-12">
                            <dl class="row">
                                <dt class="col-6">การจัดส่ง :</dt>
                                <dd class="col-6 text-right under-line"><?php echo $shipping_name; ?></dd>
                                <dt class="col-6">จำนวนแท่งรวม :</dt>
                                <dd class="col-6 text-right under-line"><?php echo $total_bars; ?> แท่ง</dd>
                                <dt class="col-6">จำนวนรายการ :</dt>
                                <dd class="col-6 text-right under-line"><?php echo count($all_orders); ?> รายการ</dd>
                                <dt class="col-6">ค่าจัดส่ง :</dt>
                                <dd class="col-6 text-right under-line"><?php echo number_format($shipping['price'] ?? 0, 2); ?> บาท</dd>
                                <dt class="col-6">ค่าธรรมเนียม :</dt>
                                <dd class="col-6 text-right under-line"><?php echo number_format($main_order['fee'] ?? 0, 2); ?> บาท</dd>
                                <dt class="col-6"><strong>รวมเงินทั้งหมด :</strong></dt>
                                <dd class="col-6 text-right under-line"><strong><?php echo number_format($grand_total, 2); ?> บาท</strong></dd>
                            </dl>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- ลายเซ็นพนักงาน -->
            <div class="signature-section keep-together">
                <table class="p-5 mt-5 small-text signature-table" width="100%">
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <div>________________________</div>
                                <div>พนักงานขาย</div>
                                <div>วันที่____________</div>
                            </td>
                            <td class="text-center">
                                <div>__________________________</div>
                                <div>พนักงานการเงิน</div>
                                <div>วันที่____________</div>
                            </td>
                            <td class="text-center">
                                <div>__________________________</div>
                                <div>พนักงานปล่อยสินค้า</div>
                                <div>วันที่____________</div>
                            </td>
                            <td class="text-center">
                                <div>__________________________</div>
                                <div>พนักงานเลเซอร์</div>
                                <div>วันที่____________</div>
                            </td>
                            <td class="text-center">
                                <div>__________________________</div>
                                <div>พนักงานแพ็คของ</div>
                                <div>วันที่____________</div>
                            </td>
                            <td class="text-center">
                                <div>__________________________</div>
                                <div>พนักงานส่ง</div>
                                <div>วันที่____________</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <br>
            <div class="d-flex align-items-center container">
                <div>
                    <img class="pull-right" src="img/bowins-footer.jpg" width="800px" height="98px">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <script>
        feather.replace();
    </script>

</body>

</html>