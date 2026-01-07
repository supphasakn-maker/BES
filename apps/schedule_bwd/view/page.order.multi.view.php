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

$remote_postcodes = [
    20120,
    23170,
    57170,
    57180,
    57230,
    57240,
    57250,
    57260,
    58000,
    58110,
    58120,
    58130,
    58140,
    58150,
    63150,
    63160,
    63170,
    71180,
    71190,
    71220,
    71230,
    71240,
    81150,
    81160,
    81170,
    81180,
    81190,
    81200,
    81210,
    82160,
    83000,
    83110,
    83120,
    83130,
    83140,
    83150,
    83151,
    84140,
    84160,
    84230,
    84240,
    84250,
    84260,
    84280,
    84290,
    84310,
    84320,
    84330,
    84340,
    84345,
    84350,
    84360,
    94000,
    94110,
    94120,
    94130,
    94140,
    94150,
    94160,
    94170,
    94180,
    94190,
    94220,
    94230,
    95000,
    95120,
    95130,
    95140,
    95150,
    95160,
    95170,
    96000,
    96110,
    96120,
    96130,
    96140,
    96150,
    96160,
    96170,
    96180,
    96190,
    96220
];

function orderable_type_label($v)
{
    $map = [
        'delivered_by_company' => 'จัดส่งโดยรถบริษัท',
        'post_office'          => 'จัดส่งโดยไปรษณีย์ไทย',
        'receive_at_company'   => 'รับสินค้าที่บริษัท',
        'receive_at_luckgems'  => 'รับสินค้าที่ Luck Gems'
    ];
    $v = trim((string)$v);
    return $map[$v] ?? '-';
}

function fmt_dmy($dateStr)
{
    if (empty($dateStr) || $dateStr === '0000-00-00' || $dateStr === '0000-00-00 00:00:00') return '-';
    $ts = strtotime($dateStr);
    if ($ts === false) return '-';
    return date("d/m/Y", $ts);
}

function extract_postcode($address)
{
    if (preg_match('/\b(\d{5})\b/', $address, $matches)) {
        return (int)$matches[1];
    }
    return null;
}

function is_remote_area($postcode, $remote_postcodes)
{
    return in_array((int)$postcode, $remote_postcodes);
}

$all_orders = [];
$all_orders_query = "SELECT * FROM bs_orders_bwd WHERE (id = $main_order_id OR parent = $main_order_id) ORDER BY box_number ASC, id ASC";
$all_orders_result = $dbc->query($all_orders_query);
if ($all_orders_result) {
    while ($row = mysqli_fetch_assoc($all_orders_result)) {
        $all_orders[] = $row;
    }
}

// จัดกลุ่มตาม box_number
$boxes = [];
foreach ($all_orders as $order) {
    $box_num = (int)$order['box_number'];
    if (!isset($boxes[$box_num])) {
        $boxes[$box_num] = [
            'orders' => [],
            'shipping_base' => 0,
            'shipping_box_fee' => 0,
            'shipping_remote_fee' => 0,
            'shipping_total' => 0
        ];
    }
    $boxes[$box_num]['orders'][] = $order;

    if (count($boxes[$box_num]['orders']) === 1) {
        $boxes[$box_num]['shipping_base'] = (float)$order['shipping_base'];
        $boxes[$box_num]['shipping_box_fee'] = (float)$order['shipping_box_fee'];
        $boxes[$box_num]['shipping_remote_fee'] = (float)$order['shipping_remote_fee'];
        $boxes[$box_num]['shipping_total'] = (float)$order['shipping_total'];
    }
}

$delivery = null;
if (!empty($main_order['delivery_id'])) {
    $delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $main_order['delivery_id']);
}

$shipping = null;
$shipping_name = '-';
if (!empty($main_order['shipping'])) {
    $shipping = $dbc->GetRecord("bs_shipping_bwd", "*", "id=" . $main_order['shipping']);
    if ($shipping) $shipping_name = $shipping['name'];
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
        $payment_note = ["bank" => $delivery['default_bank'] ?? '', "payment" => $delivery['default_payment'] ?? '', "remark" => ""];
    } else {
        $payment_note = json_decode($delivery['payment_note'], true);
        if (!is_array($payment_note)) $payment_note = ["bank" => "", "payment" => "", "remark" => ""];
    }
} else {
    $payment_note = ["bank" => "", "payment" => "", "remark" => ""];
}

$grand_total = 0;
$total_bars = 0;
$total_shipping = 0;
foreach ($all_orders as $order) {
    $grand_total += (float)$order['net'];
    $total_bars  += (float)$order['amount'];
}

foreach ($boxes as $box) {
    $total_shipping += $box['shipping_total'];
}

$delivery_date_display = fmt_dmy($main_order['delivery_date'] ?? '');
$orderable_label = orderable_type_label($main_order['orderable_type'] ?? '');

$postcode = extract_postcode($main_order['shipping_address'] ?? '');
$is_remote = $postcode ? is_remote_area($postcode, $remote_postcodes) : false;

$insurance_amount = 0;
$total_price = $grand_total;

$insurance_table = [
    2500,
    3000,
    3500,
    4000,
    4500,
    5000,
    5500,
    6000,
    6500,
    7000,
    7500,
    8000,
    8500,
    9000,
    9500,
    10000,
    10500,
    11000,
    11500,
    12000,
    12500,
    13000,
    13500,
    14000,
    14500,
    15000,
    15500,
    16000,
    16500,
    17000,
    17500,
    18000,
    18500,
    19000,
    19500,
    20000,
    20500,
    21000,
    21500,
    22000,
    22500,
    23000,
    23500,
    24000,
    24500,
    25000,
    25500,
    26000,
    26500,
    27000,
    27500,
    28000,
    28500,
    29000,
    29500,
    30000,
    30500,
    31000,
    31500,
    32000,
    32500,
    33000,
    33500,
    34000,
    34500,
    35000,
    35500,
    36000,
    36500,
    37000,
    37500,
    38000,
    38500,
    39000,
    39500,
    40000,
    40500,
    41000,
    41500,
    42000,
    42500,
    43000,
    43500,
    44000,
    44500,
    45000,
    45500,
    46000,
    46500,
    47000,
    47500,
    48000,
    48500,
    49000,
    49500,
    50000,
];

foreach ($insurance_table as $coverage) {
    if ($total_price <= $coverage) {
        $insurance_amount = $coverage;
        break;
    }
}

function calculate_insurance_for_box($box_orders, $insurance_table)
{
    $sum = 0;
    foreach ($box_orders as $o) {
        $sum += (float)$o['net'];
    }

    // หา coverage
    foreach ($insurance_table as $coverage) {
        if ($sum <= $coverage) {
            return $coverage;
        }
    }

    return end($insurance_table);
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

        .big-text-added {
            font-size: 14pt;
            font-weight: 600;
        }

        .under-line {
            border-bottom: 1px solid #000;
        }

        .flower {
            border: 2px solid #000;
            padding: 10px;
            border-radius: 4px;
        }

        .order-item {
            /* กล่องสินค้าให้ดูลอยชิดซ้าย เรียบๆ */
            background-color: #ffffff;
            margin-bottom: 15px;
            border: 1px solid #000;
            border-radius: 5px;
            padding: 10px;
            text-align: left;
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

        /* กล่องสินค้าแต่ละกล่องให้ชิดซ้าย */
        .box-section {
            margin-bottom: 15px;
            text-align: left;
        }

        .box-header {
            font-weight: 600;
            margin-bottom: 5px;
            text-align: left;
        }

        /* 🆕 Shipping Breakdown Styles (ขาวดำ) */
        .shipping-breakdown {
            background-color: #ffffff;
            border: 1px solid #000000;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .box-shipping {
            background-color: #ffffff;
            border: 1px solid #000000;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .remote-badge {
            background-color: #ffffff;
            color: #000000;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #000000;
            font-size: 10pt;
            font-weight: bold;
        }

        /* ให้ข้อความในสรุปค่าจัดส่งชิดซ้าย ไม่กลาง */
        .shipping-breakdown h6,
        .shipping-breakdown .text-center {
            text-align: left !important;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        @media print {
            body {
                font-size: 12pt;
                line-height: 1.3;
                color: #000 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .main-header,
            .sidebar,
            .breadcrumb,
            .btn-area,
            button,
            .no-print {
                display: none !important;
            }

            .container,
            .container-fluid {
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            [class*="col-"] {
                padding-left: 4px !important;
                padding-right: 4px !important;
            }

            .card,
            .card-body {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .order-item,
            .flower,
            .signature-section,
            .summary-section,
            .order-items-section,
            .shipping-breakdown,
            table,
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
                object-fit: contain;
            }

            .checkbox-item input[type="checkbox"] {
                width: 14px !important;
                height: 14px !important;
                margin-right: 8px !important;
                margin-top: 0 !important;
                -webkit-appearance: none !important;
                appearance: none !important;
                border: 2px solid #000 !important;
                background: #fff !important;
                outline: none !important;
                box-shadow: none !important;
                position: relative !important;
            }

            .checkbox-item input[type="checkbox"]::before {
                content: "";
                position: absolute;
                top: -2px;
                left: -2px;
                width: 14px;
                height: 14px;
                border: 2px solid #000;
                background: #fff;
                display: block;
            }

            .checkbox-item input[type="checkbox"]:checked::after {
                content: "✓";
                position: absolute;
                top: -1px;
                left: 1px;
                font-size: 12px;
                font-weight: 700;
                color: #000;
            }

            .checkbox-item label {
                font-size: 10pt !important;
                line-height: 1.1 !important;
            }

            a[href]:after {
                content: none !important;
            }

            a {
                color: inherit !important;
                text-decoration: none !important;
            }

            /* 🆕 Print: สรุปค่าจัดส่ง ขาวดำ */
            .shipping-breakdown {
                background-color: #ffffff !important;
                border: 1px solid #000000 !important;
            }

            .box-shipping {
                background-color: #ffffff !important;
                border: 1px solid #000000 !important;
            }

            .remote-badge {
                background-color: #ffffff !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
            }
        }
    </style>
</head>

<body>

    <div class="btn-area btn-group mb-2 no-print">
        <button type="button" class="btn btn-dark" onclick='window.history.back()'>Back</button>
        <button class="btn btn-light has-icon mt-1 mt-sm-0" type="button" onclick="window.print()">
            <i class="mr-2" data-feather="printer"></i>Print ใบยืนยันการสั่งซื้อ
        </button>
    </div>

    <div class="card">
        <div class="card-body mr-4 ml-4"></div>
        <div class="d-flex align-items-center container">
            <div>
                <img class="pull-right" src="img/logo-Bowins-design.png" width="120" height="120">
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
                        <dd class="col-6 under-line"><?php echo htmlspecialchars($main_order['platform']); ?></dd>
                        <dt class="col-4">ชื่อลูกค้า : </dt>
                        <dd class="col-7 under-line"><?php echo htmlspecialchars($main_order['customer_name']); ?></dd>
                        <dt class="col-5">Username :</dt>
                        <dd class="col-6 under-line"><?php echo htmlspecialchars($cus); ?></dd>
                        <dt class="col-5">โทร : </dt>
                        <dd class="col-6 under-line"><?php echo htmlspecialchars($main_order['phone']); ?></dd>
                    </dl>
                </div>
                <div class="col-sm-5">
                    <dl class="row col-8 offset-8">
                        <dt class="col-4">วันที่ซื้อ :</dt>
                        <dd class="col-7 under-line"><?php echo fmt_dmy($main_order['date']); ?></dd>
                        <dt class="col-4">วันที่คีย์ :</dt>
                        <dd class="col-7 under-line"><?php echo fmt_dmy($main_order['created']); ?></dd>
                        <dt class="col-4">No :</dt>
                        <dd class="col-8 under-line"><?php echo htmlspecialchars($main_order['code']); ?></dd>
                    </dl>
                </div>
            </div>

            <div class="row small-text">
                <div class="col-sm-12">
                    <dl class="row col-12">
                        <dt class="col-3">ผู้ขาย :</dt>
                        <dd class="col-4 under-line" style="font-family: cursive;"><?php echo htmlspecialchars($signature); ?></dd>
                        <dt class="col-3">การชำระเงิน :</dt>
                        <dd class="col-2 under-line"><?php echo htmlspecialchars($payment_note['bank']); ?></dd>

                        <dt class="col-3">เงื่อนไขการชำระเงิน :</dt>
                        <dd class="col-4 under-line"><?php echo htmlspecialchars($payment_note['payment']); ?></dd>
                        <dt class="col-3">หมายเหตุ :</dt>
                        <dd class="col-2 under-line"><?php echo htmlspecialchars($payment_note['remark']); ?></dd>

                        <dt class="col-3 big-text-added mt-2">วันที่จัดส่ง :</dt>
                        <dd class="col-4 under-line big-text-added mt-2"><?php echo $delivery_date_display; ?></dd>
                        <dt class="col-3">เลข Orders Platform :</dt>
                        <dd class="col-2 under-line "><?php echo htmlspecialchars($main_order['order_platform']); ?></dd>
                    </dl>
                </div>
            </div>

            <!-- ที่อยู่ -->
            <div class="row small-text flower">
                <div class="col-12">
                    <dl class="row col-10 mt-2">
                        <dt class="col-3">ที่อยู่ออกอินวอยซ์ :</dt>
                        <dd class="col-8 under-line"><?php echo htmlspecialchars($main_order['billing_address']); ?></dd>
                        <dt class="col-3">ที่อยู่จัดส่ง :</dt>
                        <dd class="col-8 under-line">
                            <?php echo htmlspecialchars($main_order['shipping_address']); ?>
                            <?php if ($postcode): ?>
                                <strong>(รหัสไปรษณีย์: <?php echo $postcode; ?>)</strong>
                            <?php endif; ?>
                            <?php if ($is_remote && $main_order['orderable_type'] === 'post_office'): ?>
                                <span class="remote-badge">พื้นที่ห่างไกล</span>
                            <?php endif; ?>
                        </dd>
                        <dt class="col-3">รูปแบบการจัดส่ง :</dt>
                        <dd class="col-8 under-line"><?php echo htmlspecialchars($orderable_label); ?></dd>
                    </dl>
                </div>
            </div>
            <div class="row small-text flower mt-3 order-items-section">
                <div class="col-12">
                    <h5 class="text-center mb-3">รายการสินค้า (<?php echo count($all_orders); ?> รายการ / <?php echo count($boxes); ?> กล่อง)</h5>

                    <?php foreach ($boxes as $box_number => $box_data): ?>
                        <div class="box-section keep-together">
                            <?php
                            $box_insurance = calculate_insurance_for_box($box_data['orders'], $insurance_table);
                            ?>

                            <div class="box-header mb-1">
                                กล่องที่ <?php echo ($box_number + 1); ?>
                                (<?php echo count($box_data['orders']); ?> รายการ)
                            </div>

                            <div class="ml-3 mb-2 small-text">
                                วงเงินรับประกันสินค้า:
                                <strong><?php echo number_format($box_insurance, 2); ?> บาท</strong>
                            </div>

                            <?php
                            $global_index = 0;
                            foreach ($all_orders as $idx => $ord) {
                                if ((int)$ord['box_number'] < $box_number) $global_index++;
                            }
                            ?>

                            <?php foreach ($box_data['orders'] as $local_index => $order): ?>
                                <?php
                                $product = $dbc->GetRecord("bs_products_bwd", "*", "id=" . (int)$order['product_id']);
                                $product_name = $product ? $product['name'] : '-';

                                $product_type = $dbc->GetRecord("bs_products_type", "*", "id=" . (int)$order['product_type']);
                                $product_type_name = $product_type ? $product_type['name'] : '-';

                                $engrave_fee = ($order['engrave'] == "สลักข้อความบนแท่งเงิน") ? (300 * (float)$order['amount']) : 0;
                                $ai = ($order['ai'] == "1") ? (400 * (float)$order['amount']) : 0;

                                $img = '';
                                $aa = 'แท่ง';
                                switch ((int)$order['product_type']) {
                                    case 1:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__15กรัม.png" width="80" height="80">';
                                        break;
                                    case 2:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__50กรัม_logo_นก.png" width="80" height="80">';
                                        break;
                                    case 3:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__50กรัม_no_logo_นก.png" width="80" height="80">';
                                        break;
                                    case 4:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__50กรัม_logo_เสือ.png" width="80" height="80">';
                                        break;
                                    case 5:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__50กรัม_no_logo_เสือ.png" width="80" height="80">';
                                        break;
                                    case 6:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__50กรัม_logo_มังกร.png" width="80" height="80">';
                                        break;
                                    case 7:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__50กรัม_no_logo_มังกร.png" width="80" height="80">';
                                        break;
                                    case 8:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__150กรัม_นก.png" width="80" height="80">';
                                        break;
                                    case 9:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__150กรัม_เสือ.png" width="80" height="80">';
                                        break;
                                    case 10:
                                        $img = '<img class="product-img" src="img/bwd/AW_silver__150กรัม_มังกร.png" width="80" height="80">';
                                        break;
                                    case 13:
                                        $img = '<img class="product-img" src="img/bwd/AW_กล่องดอกไม้_กล่อง_50กรัม_ ดอกไม้.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 14:
                                        $img = '<img class="product-img" src="img/bwd/AW_กล่องดอกไม้_กล่อง_50กรัม_ ดอกไม้.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 15:
                                        $img = '<img class="product-img" src="img/bwd/AW_กล่องดอกไม้_กล่อง_150กรัม_ ดอกไม้แดง.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 16:
                                        $img = '<img class="product-img" src="img/bwd/AW_กล่องดอกไม้_กล่อง_50กรัม_ ดอกไม้เงิน.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 17:
                                        $img = '<img class="product-img" src="img/bwd/กล่องไม้_1 pcs.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 18:
                                        $img = '<img class="product-img" src="img/bwd/กล่องไม้_2 pcs.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 19:
                                        $img = '<img class="product-img" src="img/bwd/กล่องไม้_3 pcs.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 20:
                                        $img = '<img class="product-img" src="img/bwd/กล่องไม้_4 pcs.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 22:
                                        $img = '<img class="product-img" src="img/bwd/กล่อง_139_กล่อง_15กรัม.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 23:
                                        $img = '<img class="product-img" src="img/bwd/กล่อง_139_กล่อง_50กรัม.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                    case 24:
                                        $img = '<img class="product-img" src="img/bwd/กล่อง_139_กล่อง_150กรัม.png" width="80" height="80">';
                                        $aa = 'กล่อง';
                                        break;
                                }

                                $global_index++;
                                ?>

                                <div class="order-item mb-3 p-3">
                                    <div class="row">
                                        <div class="col-md-2 text-left">
                                            <?php echo $img; ?>
                                            <div class="mt-2"><strong>รายการที่ <?php echo $global_index; ?></strong></div>
                                        </div>
                                        <div class="col-md-5">
                                            <dl class="row">
                                                <dt class="col-5">ขนาดแท่ง :</dt>
                                                <dd class="col-7"><?php echo htmlspecialchars($product_name); ?></dd>
                                                <dt class="col-5">ลาย :</dt>
                                                <dd class="col-7"><?php echo htmlspecialchars($product_type_name); ?></dd>
                                                <dt class="col-5">จำนวน :</dt>
                                                <dd class="col-7"><?php echo (float)$order['amount']; ?> <?php echo $aa; ?></dd>
                                                <dt class="col-5">การสลักข้อความ :</dt>
                                                <dd class="col-7"><?php echo htmlspecialchars($order['engrave']); ?></dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-3">
                                            <dl class="row">
                                                <dt class="col-6">Font :</dt>
                                                <dd class="col-6"><?php echo htmlspecialchars($order['font']); ?></dd>
                                                <dt class="col-6">Text :</dt>
                                                <dd class="col-6"><?php echo htmlspecialchars($order['carving']); ?></dd>
                                                <dt class="col-6">LASER เพิ่ม :</dt>
                                                <dd class="col-6"><?php echo ($order['ai'] == '1') ? 'ใช่' : 'ไม่'; ?></dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-2">
                                            <dl class="row">
                                                <dt class="col-12 text-right">ราคาต่อแท่ง :</dt>
                                                <dd class="col-12 text-right"><?php echo number_format((float)$order['price'], 2); ?> บาท</dd>
                                                <dt class="col-12 text-right">ส่วนลด :</dt>
                                                <dd class="col-12 text-right"><?php echo number_format((float)$order['discount'], 2); ?> บาท</dd>
                                                <dt class="col-12 text-right">ค่าสลัก :</dt>
                                                <dd class="col-12 text-right"><?php echo number_format($engrave_fee, 2); ?> บาท</dd>
                                                <dt class="col-12 text-right">ค่าเลเซอร์ :</dt>
                                                <dd class="col-12 text-right"><?php echo number_format($ai, 2); ?> บาท</dd>
                                                <dt class="col-12 text-right"><strong>รวม :</strong></dt>
                                                <dd class="col-12 text-right"><strong><?php echo number_format((float)$order['net'], 2); ?> บาท</strong></dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="box-shipping">
                                <strong>ค่าจัดส่งกล่องที่ <?php echo ($box_number + 1); ?>:</strong><br>
                                <div class="row mt-2 small-text">
                                    <div class="col-6">
                                        • ค่าส่งพื้นฐาน: <strong><?php echo number_format($box_data['shipping_base'], 2); ?> บาท</strong><br>
                                        • ค่ากล่องพิเศษ: <strong><?php echo number_format($box_data['shipping_box_fee'], 2); ?> บาท</strong>
                                    </div>
                                    <div class="col-6">
                                        • ค่าพื้นที่ห่างไกล: <strong><?php echo number_format($box_data['shipping_remote_fee'], 2); ?> บาท</strong><br>
                                        • <strong>รวมค่าส่งกล่องนี้: <?php echo number_format($box_data['shipping_total'], 2); ?> บาท</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($boxes) > 1): ?>
                        <div class="shipping-breakdown keep-together">
                            <h6 class="mb-3"><strong>สรุปค่าจัดส่งทั้งหมด</strong></h6>
                            <div class="row small-text">
                                <?php
                                $total_base = 0;
                                $total_box_fee = 0;
                                $total_remote = 0;
                                foreach ($boxes as $box) {
                                    $total_base += $box['shipping_base'];
                                    $total_box_fee += $box['shipping_box_fee'];
                                    $total_remote += $box['shipping_remote_fee'];
                                }
                                ?>
                                <div class="col-4">
                                    <strong>ค่าส่งพื้นฐาน:</strong><br>
                                    <?php echo number_format($total_base, 2); ?> บาท
                                </div>
                                <div class="col-4">
                                    <strong>ค่ากล่องพิเศษ:</strong><br>
                                    <?php echo number_format($total_box_fee, 2); ?> บาท
                                </div>
                                <div class="col-4">
                                    <strong>ค่าพื้นที่ห่างไกล:</strong><br>
                                    <strong><?php echo number_format($total_remote, 2); ?> บาท</strong>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <h5><strong>รวมค่าจัดส่งทั้งหมด: <?php echo number_format($total_shipping, 2); ?> บาท</strong></h5>
                            </div>
                        </div>
                    <?php endif; ?>
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
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check1"><label class="form-check-label" for="check1">แท่งเปล่า</label></div>
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check2"><label class="form-check-label" for="check2">ซองกันกระแทก</label></div>
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check3"><label class="form-check-label" for="check3">ซองพลาสติก</label></div>
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check4"><label class="form-check-label" for="check4">Certificate Card</label></div>
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check5"><label class="form-check-label" for="check5">Care Card</label></div>
                                </div>
                                <div class="col-6">
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check6"><label class="form-check-label" for="check6">About Artist Card</label></div>
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check7"><label class="form-check-label" for="check7">ผ้าเช็ดแท่งเงิน</label></div>
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check8"><label class="form-check-label" for="check8">ถุงใหญ่</label></div>
                                    <div class="checkbox-item"><input class="form-check-input" type="checkbox" id="check9"><label class="form-check-label" for="check9">ถุงเล็ก</label></div>
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
                                <dd class="col-6 text-right under-line"><?php echo htmlspecialchars($shipping_name); ?></dd>
                                <dt class="col-6">จำนวนแท่งรวม :</dt>
                                <dd class="col-6 text-right under-line"><?php echo $total_bars; ?> แท่ง</dd>
                                <dt class="col-6">จำนวนรายการ :</dt>
                                <dd class="col-6 text-right under-line"><?php echo count($all_orders); ?> รายการ</dd>
                                <dt class="col-6">จำนวนกล่อง :</dt>
                                <dd class="col-6 text-right under-line"><?php echo count($boxes); ?> กล่อง</dd>
                                <dt class="col-6">ค่าจัดส่งรวม :</dt>
                                <dd class="col-6 text-right under-line"><strong><?php echo number_format($total_shipping, 2); ?> บาท</strong></dd>
                                <dt class="col-6">ค่าธรรมเนียม :</dt>
                                <dd class="col-6 text-right under-line"><?php echo number_format((float)($main_order['fee'] ?? 0), 2); ?> บาท</dd>
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
                    <img class="pull-right" src="img/bowins-footer.jpg" width="800" height="98">
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