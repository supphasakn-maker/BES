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

$main_order['code']             = $main_order['code']             ?? '';
$main_order['customer_name']    = $main_order['customer_name']    ?? '';
$main_order['phone']            = $main_order['phone']            ?? '';
$main_order['shipping_address'] = $main_order['shipping_address'] ?? '';
$main_order['date']             = $main_order['date']             ?? date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>ใบปะหน้า 100×150</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


</head>
<style>
    @media print {
        @page {
            size: 100mm 150mm;
            margin: 0;
        }

        html,
        body {
            width: 100mm;
            height: 150mm;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        body * {
            visibility: hidden;
        }

        #shipping-label,
        #shipping-label * {
            visibility: visible;
        }

        #shipping-label {
            position: relative;
            width: 100mm;
            height: 150mm;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

   

    .label-100x150 {
        width: 90mm;
        height: 140mm;
        padding: 6mm;
        box-sizing: border-box;
        border: 1px solid #ccc;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        text-align: center;
        font-size: 15pt;
    }

    .sender-title {
        font-size: 22pt;
        font-weight: 800;
        margin-bottom: 4mm;
    }

    .sender-logo img {
        height: 40mm;
        width: auto;
        margin-bottom: 4mm;
    }

    .sender-address {
        font-size: 15pt;
        line-height: 1.4;
        margin-bottom: 4mm;
    }

    .divider {
        border: none;
        border-top: 2px dashed #000;
        width: 90%;
        margin: 5mm 0;
    }

    .receiver-section {
        width: 100%;
        font-size: 15pt;
        text-align: left;
    }

    .receiver-title {
        text-align: center;
        font-size: 19pt;
        font-weight: 800;
        margin-bottom: 4mm;
    }

    .receiver-info {
        margin-bottom: 4mm;
    }

    .receiver-address {
        border: 2px dashed #000;
        padding: 4mm;
        min-height: 30mm;
        font-size: 15pt;
        line-height: 1.4;
    }
</style>

<body>

    <div class="toolbar">
        <button class="btn" onclick="history.back()">Back</button>
        <button class="btn" onclick="window.print()">Print</button>
    </div>
    <div id="shipping-label">
        <div class="label-100x150">
            <div class="sender-title">ผู้ส่ง บริษัท โบวินส์</div>
            <div class="sender-logo">
                <img src="img/logo-Bowins-design.png" alt="Bowins">
            </div>
            <div class="sender-address">
                เลขที่ 74/1 ซอยสุขาภิบาล 2 ซอย 31 แขวงดอกไม้ เขตประเวศ<br>
                กรุงเทพมหานคร 10250
            </div>

            <hr class="divider">

            <div class="receiver-section">
                <div class="receiver-title">ผู้รับ</div>
                <div class="receiver-info">
                    <div><b>OD :</b> <?= htmlspecialchars($main_order['code']); ?></div>
                    <div><b>ชื่อผู้รับ :</b> <?= htmlspecialchars($main_order['customer_name']); ?></div>
                    <div><b>โทร :</b> <?= htmlspecialchars($main_order['phone']); ?></div>
                </div>
                <div><b>ที่อยู่ :</b></div>
                <div class="receiver-address">
                    <?= nl2br(htmlspecialchars($main_order['shipping_address'])); ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>