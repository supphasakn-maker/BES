<?php
$date  = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$start = $date . ' 00:00:00';
$end   = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));

$total_amount   = 0;
$total_price      = 0;
$total_fee      = 0;
$total_total    = 0;
$total_net      = 0;
$total_discount = 0;
$total_kg       = 0;
$counter        = 0;

$sql_parent = "
    SELECT  
        parent.id,
        parent.sales,
        parent.code,
        parent.customer_name,
        parent.product_id,
        parent.delivery_date,
        parent.date AS date,
        parent.created AS created_dt,

        SUM(o.amount)   AS amount,
        SUM(o.price)    AS price,
        SUM(o.discount) AS discount,
        SUM(o.fee)      AS fee,
        SUM(o.net)      AS net,
        SUM(o.total)    AS total,

        ANY_VALUE(o.platform) AS platform,
        p.name AS product_name,

        COALESCE(SUM(
            CASE 
                WHEN o.product_id = 1 THEN o.amount * 0.015
                WHEN o.product_id = 2 THEN o.amount * 0.050
                WHEN o.product_id = 3 THEN o.amount * 0.150
                ELSE 0
            END
        ),0) AS weight_kg

    FROM bs_orders_bwd parent
    LEFT JOIN bs_orders_bwd o 
        ON (o.id = parent.id OR o.parent = parent.id)
    LEFT JOIN bs_products_bwd p 
        ON parent.product_id = p.id
    WHERE parent.parent IS NULL
      AND o.status > 0
      AND parent.date >= '" . $dbc->Escape_String($start) . "'
      AND parent.date <  '" . $dbc->Escape_String($end) . "'
    GROUP BY parent.id
    ORDER BY parent.id DESC
";
$rst_parent = $dbc->Query($sql_parent);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>ภาพรวมการขายประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?></title>
    <style>
        body {
            font-family: system-ui, Tahoma, sans-serif;
            font-size: 14px;
            color: #e5e7eb;
            background: #0f172a;
        }

        .sales-summary-wrap {
            overflow-x: auto;
        }

        /* ใช้เฉพาะตารางนี้ */
        .sales-summary-table,
        .sales-summary-table .subtable {
            width: 100%;
            /* ให้กว้างพอ หัวข้อจะไม่ถูกบีบ */
            border-collapse: collapse;
            table-layout: fixed;
            color: #e5e7eb;
            background: #111827;
        }

        .sales-summary-table th,
        .sales-summary-table td {
            border: 1px solid #2b3443;
            padding: 6px 8px;
            vertical-align: top;
            white-space: nowrap;
            /* ไม่ให้ตัดขึ้นบรรทัดใหม่ */
            overflow: hidden;
            text-overflow: ellipsis;
            /* เกินให้ … */
        }

        .sales-summary-table thead th {
            background: #0b1220;
            color: #fff;
            font-weight: 700;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        /* แถวแม่ */
        .sales-summary-table .parent-row {
            background: #00204E;
            /* กรม */
            color: #cfe8ff;
            font-weight: 700;
            border-left: 6px solid #00B4D8;
        }

        /* ให้ช่องแรกของแถวแม่ (PO) เด่น: ตัวดำ พื้นฟ้าอ่อน */
        .sales-summary-table .parent-row td:first-child {
            background: #E3F2FD;
            color: #000;
        }

        /* ตารางลูก */
        .sales-summary-table .subcell {
            padding: 0;
            background: #0f172a;
        }

        .sales-summary-table .subtable th,
        .sales-summary-table .subtable td {
            border: 1px solid #2b3443;
            padding: 6px 8px;
            background: #0f172a;
            color: #dbe2ef;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sub-parent {
            font-weight: 700;
            border-left: 4px solid #00B4D8;
            background: #08233a !important;
        }

        .sub-child {
            background: #0d2036 !important;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 12px;
            line-height: 1;
            margin-right: 6px;
        }

        .badge-parent {
            background: #00B4D8;
            color: #00204E;
            font-weight: 700;
        }

        .badge-child {
            background: #334155;
            color: #e5e7eb;
        }

        /* ความกว้างคอลัมน์ (แม่และลูกเท่ากัน) */
        .sales-summary-table col.w1 {
            width: 10%;
        }

        /* PO / LEVEL */
        .sales-summary-table col.w2 {
            width: 12%;
        }

        /* CUSTOMER / ORDER NO. */
        .sales-summary-table col.w3 {
            width: 10%;
        }

        /* PLATFORM / PRODUCT */
        .sales-summary-table col.w4 {
            width: 12%;
        }

        /* PRODUCT / PRODUCT TYPE */
        .sales-summary-table col.w5 {
            width: 7%;
        }

        /* DETAIL / BARS */
        .sales-summary-table col.w6 {
            width: 7%;
        }

        /* BARS / KGS. */
        .sales-summary-table col.w7 {
            width: 9%;
        }

        /* DATE PURCHASE / BATH/KGS */
        .sales-summary-table col.w8 {
            width: 8%;
        }

        /* KGS. / DISCOUNT */
        .sales-summary-table col.w9 {
            width: 7%;
        }

        /* BATH / KGS. / FEE */
        .sales-summary-table col.w10 {
            width: 8%;
        }

        /* DISCOUNT / TOTAL */
        .sales-summary-table col.w11 {
            width: 8%;
        }

        /* FEE / TOTAL-DISCOUNT */
        /* คอลัมน์ที่เหลือของหัวตาราง (DELIVERY, SALE, ACTION) ให้กว้างอัตโนมัติ */
    </style>
</head>

<body>

    <div class="sales-summary-wrap">
        <table class="sales-summary-table">
            <colgroup>
                <col class="w1">
                <col class="w2">
                <col class="w3">
                <col class="w4">
                <col class="w5">
                <col class="w6">
                <col class="w7">
                <col class="w8">
                <col class="w9">
                <col class="w10">
                <col class="w11">
            </colgroup>
            <thead>
                <tr>
                    <th colspan="16" class="text-center font-weight-bold">
                        ภาพรวมการขายประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?>
                    </th>
                </tr>
                <tr>
                    <th>PO</th>
                    <th>CUSTOMER</th>
                    <th>PLATFORM</th>
                    <th>PRODUCT</th>
                    <th>DETAIL</th>
                    <th>BARS</th>
                    <th>DATE PURCHASE</th>
                    <th>KGS.</th>
                    <th>BATH / KGS.</th>
                    <th>DISCOUNT</th>
                    <th>FEE</th>
                    <th>TOTAL</th>
                    <th>TOTAL - DISCOUNT</th>
                    <th>DELIVERY DATE</th>
                    <th>SALE</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $dbc->Fetch($rst_parent)):
                    $parent_id = (int)$order['id'];

                    $sql_sub = "
                SELECT
                    o.id, o.parent, o.code AS order_no,
                    CASE WHEN o.parent IS NULL THEN 'PARENT' ELSE 'CHILD' END AS lvl,
                    COALESCE(p2.name,'-') AS product_name,
                    COALESCE(pt.name,'-') AS type_name,
                    o.amount AS bar_amt,
                    (CASE
                        WHEN o.product_id=1 THEN o.amount*0.015
                        WHEN o.product_id=2 THEN o.amount*0.050
                        WHEN o.product_id=3 THEN o.amount*0.150
                        ELSE 0
                    END) AS kg_amt,
                    o.price AS price_amt,
                    o.discount AS disc_amt,
                    o.fee AS fee_amt,
                    o.total AS total_amt,
                    o.net   AS net_amt
                FROM bs_orders_bwd o
                LEFT JOIN bs_products_bwd  p2 ON p2.id = o.product_id
                LEFT JOIN bs_products_type pt ON pt.id = o.product_type
                WHERE (o.id = {$parent_id} OR o.parent = {$parent_id})
                  AND o.status > 0
                ORDER BY (o.parent IS NULL) DESC, o.id
            ";
                    $rst_sub = $dbc->Query($sql_sub);
                ?>
                    <!-- แถวแม่ -->
                    <tr class="parent-row">
                        <td class="text-left font-weight-bold">
                            <span class="badge badge-parent">PARENT</span>
                            <?php echo htmlspecialchars($order['code']); ?>
                        </td>
                        <td class="text-left"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td class="text-left"><?php echo htmlspecialchars($order['platform']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td class="text-center">—</td>
                        <td class="text-right"><?php echo number_format((float)$order['amount'], 4); ?></td>
                        <td class="text-right"><?php echo date("d/m/Y H:i", strtotime($order['date'])); ?></td>
                        <td class="text-right"><?php echo number_format((float)$order['weight_kg'], 4); ?></td>
                        <td class="text-right"><?php echo number_format((float)$order['price'], 2); ?></td>
                        <td class="text-right"><?php echo number_format((float)$order['discount'], 2); ?></td>
                        <td class="text-right"><?php echo number_format((float)$order['fee'], 2); ?></td>
                        <td class="text-right"><?php echo number_format((float)$order['total'], 2); ?></td>
                        <td class="text-right"><?php echo number_format((float)$order['net'], 2); ?></td>
                        <td class="text-left"><?php echo htmlspecialchars((string)$order['delivery_date']); ?></td>
                        <td class="text-left">
                            <?php
                            if ($order['sales'] != "") {
                                $employee = $dbc->GetRecord("os_users", "*", "id=" . intval($order['sales']));
                                echo $employee ? htmlspecialchars($employee['display']) : "-";
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <button onclick="fn.app.sales_screen_bwd_2.multiorder.dialog_remove_each(<?php echo $parent_id; ?>)" class="btn btn-xs btn-outline-light btn-icon"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- แถวลูก -->
                    <tr>
                        <td class="subcell" colspan="16">
                            <table class="subtable">
                                <colgroup>
                                    <col class="w1">
                                    <col class="w2">
                                    <col class="w3">
                                    <col class="w4">
                                    <col class="w5">
                                    <col class="w6">
                                    <col class="w7">
                                    <col class="w8">
                                    <col class="w9">
                                    <col class="w10">
                                    <col class="w11">
                                </colgroup>
                                <thead>
                                    <tr style="background:#0b1220;">
                                        <th>LEVEL</th>
                                        <th>ORDER NO.</th>
                                        <th>PRODUCT</th>
                                        <th>PRODUCT TYPE</th>
                                        <th>BARS</th>
                                        <th>KGS.</th>
                                        <th>BATH / KGS.</th>
                                        <th>DISCOUNT</th>
                                        <th>FEE</th>
                                        <th>TOTAL</th>
                                        <th>TOTAL - DISCOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sub_sum_bar = $sub_sum_kg = $sub_sum_price = $sub_sum_disc = $sub_sum_fee = $sub_sum_total = $sub_sum_net = 0;
                                    while ($d = $dbc->Fetch($rst_sub)) {
                                        $is_parent = ($d['lvl'] === 'PARENT');
                                        $row_class = $is_parent ? 'sub-parent' : 'sub-child';
                                        $badge = $is_parent
                                            ? '<span class="badge badge-parent">PARENT</span>'
                                            : '<span class="badge badge-child">CHILD</span>';

                                        $sub_sum_bar   += (float)$d['bar_amt'];
                                        $sub_sum_kg    += (float)$d['kg_amt'];
                                        $sub_sum_price += (float)$d['price_amt'];
                                        $sub_sum_disc  += (float)$d['disc_amt'];
                                        $sub_sum_fee   += (float)$d['fee_amt'];
                                        $sub_sum_total += (float)$d['total_amt'];
                                        $sub_sum_net   += (float)$d['net_amt'];

                                        echo '<tr class="' . $row_class . '">';
                                        echo '<td>' . $badge . '</td>';
                                        echo '<td>' . htmlspecialchars($d['order_no']) . '</td>';
                                        echo '<td>' . htmlspecialchars($d['product_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($d['type_name']) . '</td>';
                                        echo '<td class="text-right">' . number_format((float)$d['bar_amt'], 4) . '</td>';
                                        echo '<td class="text-right">' . number_format((float)$d['kg_amt'], 4) . '</td>';
                                        echo '<td class="text-right">' . number_format((float)$d['price_amt'], 2) . '</td>';
                                        echo '<td class="text-right">' . number_format((float)$d['disc_amt'], 2) . '</td>';
                                        echo '<td class="text-right">' . number_format((float)$d['fee_amt'], 2) . '</td>';
                                        echo '<td class="text-right">' . number_format((float)$d['total_amt'], 2) . '</td>';
                                        echo '<td class="text-right">' . number_format((float)$d['net_amt'], 2) . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">รวม</th>
                                        <th class="text-right"><?php echo number_format($sub_sum_bar, 4); ?></th>
                                        <th class="text-right"><?php echo number_format($sub_sum_kg, 4); ?></th>
                                        <th class="text-right"><?php echo number_format($sub_sum_price, 2); ?></th>
                                        <th class="text-right"><?php echo number_format($sub_sum_disc, 2); ?></th>
                                        <th class="text-right"><?php echo number_format($sub_sum_fee, 2); ?></th>
                                        <th class="text-right"><?php echo number_format($sub_sum_total, 2); ?></th>
                                        <th class="text-right"><?php echo number_format($sub_sum_net, 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>

                <?php
                    $total_amount   += (float)$order['amount'];
                    $total_price += (float)$order['price'];
                    $total_discount += (float)$order['discount'];
                    $total_total    += (float)$order['total'];
                    $total_net      += (float)$order['net'];
                    $total_fee      += (float)$order['fee'];
                    $total_kg       += (float)$order['weight_kg'];
                    $counter++;
                endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-center">รวมทั้งหมด <?php echo $counter; ?> รายการ</th>
                    <th class="text-center"><?php echo number_format($total_amount, 4); ?></th>
                    <th class="text-right"><?php echo number_format($total_kg, 4); ?></th>
                    <th class="text-right"><?php echo number_format($total_price, 2); ?></th>
                    <th class="text-right"><?php echo number_format($total_discount, 2); ?></th>
                    <th class="text-right"><?php echo number_format($total_fee, 2); ?></th>
                    <th class="text-right"><?php echo number_format($total_total, 2); ?></th>
                    <th class="text-right" colspan="4"><?php echo number_format($total_net, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>

</html>