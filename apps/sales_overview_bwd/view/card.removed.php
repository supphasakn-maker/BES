<?php
$date  = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$start = $date . ' 00:00:00';
$end   = date('Y-m-d H:i:s', strtotime($date . ' +1 day')); 

$total_amount = 0;
$total_vat = 0;
$total_total = 0;
$total_net = 0;
$total_kg = 0;
$counter = 0;

$sql = "
    SELECT  
        parent.id,
        parent.sales,
        parent.code,
        parent.customer_name,
        parent.product_id,
        parent.delivery_date,
        parent.remove_reason,
        parent.updated AS updated_dt,

        SUM(o.amount) AS amount,
        SUM(o.price) AS price,
        SUM(o.discount) AS discount,
        SUM(o.total) AS total,
        SUM(o.net) AS net,
        COALESCE(SUM(
            CASE 
                WHEN o.product_id = 1 THEN o.amount * 0.015
                WHEN o.product_id = 2 THEN o.amount * 0.050
                WHEN o.product_id = 3 THEN o.amount * 0.150
                ELSE 0
            END
        ), 0) AS weight_kg,

        p.name,
        pt.name AS name_type

    FROM bs_orders_bwd parent
    LEFT JOIN bs_orders_bwd o 
        ON (o.id = parent.id OR o.parent = parent.id)
    LEFT JOIN bs_products_bwd p 
        ON parent.product_id = p.id
    LEFT JOIN bs_products_type pt 
        ON parent.product_type = pt.id
    WHERE parent.parent IS NULL
      AND o.status = -1
      AND parent.updated >= '" . $dbc->Escape_String($start) . "'
      AND parent.updated <  '" . $dbc->Escape_String($end) . "'
    GROUP BY parent.id
    ORDER BY parent.updated ASC
";
$rst = $dbc->Query($sql);
?>

<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap text-danger">
    <thead class="bg-danger">
        <tr>
            <th class="text-center text-white font-weight-bold" colspan="13">
                รายการที่ถูกลบประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?>
            </th>
        </tr>
        <tr>
            <th class="text-center text-white font-weight-bold">PO</th>
            <th class="text-center text-white font-weight-bold">CUSTOMER</th>
            <th class="text-center text-white font-weight-bold">PRODUCT</th>
            <th class="text-center text-white font-weight-bold">PRODUCT TYPE</th>
            <th class="text-center text-white font-weight-bold">BARS</th>
            <th class="text-center text-white font-weight-bold">KGS.</th> 
            <th class="text-center text-white font-weight-bold">BATH / KGS.</th>
            <th class="text-center text-white font-weight-bold">DISCOUNT</th>
            <th class="text-center text-white font-weight-bold">TOTAL</th>
            <th class="text-center text-white font-weight-bold">TOTAL - DISCOUNT</th>
            <th class="text-center text-white font-weight-bold">DELIVERY DATE</th>
            <th class="text-center text-white font-weight-bold">SALE</th>
            <th class="text-center text-white font-weight-bold">REMARK</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-center">' . htmlspecialchars($order['code']) . '</td>';
            echo '<td class="text-center">' . htmlspecialchars($order['customer_name']) . '</td>';
            echo '<td class="text-center">' . htmlspecialchars($order['name']) . '</td>';
            echo '<td class="text-center">' . htmlspecialchars($order['name_type']) . '</td>';
            echo '<td class="text-right">' . number_format((float)$order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format((float)$order['weight_kg'], 4) . '</td>'; 
            echo '<td class="text-right">' . number_format((float)$order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format((float)$order['discount'], 2) . '</td>';
            echo '<td class="text-right">' . number_format((float)$order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format((float)$order['net'], 2) . '</td>';
            echo '<td class="text-center">' . htmlspecialchars($order['delivery_date']) . '</td>';

            echo '<td class="text-center">';
            if ($order['sales'] != "") {
                $employee = $dbc->GetRecord("os_users", "*", "id=" . intval($order['sales']));
                echo $employee ? htmlspecialchars($employee['display']) : "-";
            } else {
                echo "-";
            }
            echo '</td>';

            echo '<td class="text-left">';
            echo '<div><strong>' . htmlspecialchars($order['remove_reason']) . '</strong></div>';
            echo '<div class="text-muted small">' . date("d/m/Y H:i", strtotime($order['updated_dt'])) . '</div>';
            echo '</td>';

            echo '</tr>';

            // รวมยอด
            $total_amount += (float)$order['amount'];
            $total_vat    += (float)$order['discount'];
            $total_total  += (float)$order['total'];
            $total_net    += (float)$order['net'];
            $total_kg     += (float)$order['weight_kg'];
            $counter++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="4">
                จากเอกสารทั้งหมด <?php echo $counter; ?> รายการ
            </th>
            <th class="text-center"><?php echo number_format($total_amount, 4); ?></th>
            <th class="text-center"><?php echo number_format($total_kg, 4); ?></th> 
            <th class="text-center"></th>
            <th class="text-center"><?php echo number_format($total_vat, 2); ?></th>
            <th class="text-center"><?php echo number_format($total_total, 2); ?></th>
            <th class="text-center"><?php echo number_format($total_net, 2); ?></th>
            <th class="text-center" colspan="3"></th>
        </tr>
    </tfoot>
</table>