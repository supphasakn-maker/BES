<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap text-danger">
    <thead class="bg-danger">
        <tr>
            <th class="text-center text-white font-weight-bold" colspan="12">รายการที่ถูกลบ <?php echo date("d/m/Y", strtotime($date)); ?></th>
        </tr>
        <tr>
            <th class="text-center text-white font-weight-bold">PO</th>
            <th class="text-center text-white font-weight-bold">CUSTOMER</th>
            <th class="text-center text-white font-weight-bold">PRODUCT</th>
            <th class="text-center text-white font-weight-bold">PRODUCT TYPE</th>
            <th class="text-center text-white font-weight-bold">BARS</th>
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
        $total_amount = 0;
        $total_vat = 0;
        $total_total = 0;
        $total_net = 0;
        $counter = 0;

        $sql = "SELECT  
            parent.id, parent.sales, parent.code, parent.customer_name, parent.product_id,
            SUM(o.amount) as amount, 
            SUM(o.price) as price, 
            SUM(o.discount) as discount, 
            SUM(o.net) as net,
            parent.remove_reason,
            SUM(o.total) as total, 
            parent.delivery_date, 
            p.name, 
            pt.name AS name_type
            FROM bs_orders_bwd parent
            LEFT JOIN bs_orders_bwd o ON (o.id = parent.id OR o.parent = parent.id)
            LEFT OUTER JOIN bs_products_bwd p ON parent.product_id = p.id
            LEFT OUTER JOIN bs_products_type pt ON parent.product_type = pt.id
            WHERE DATE(parent.date) LIKE '" . $date . "' 
            AND parent.parent IS NULL
            AND o.status = -1
            GROUP BY parent.id";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-Center">' . $order['code'] . '</td>';
            echo '<td class="text-Center">' . $order['customer_name'] . '</td>';
            echo '<td class="text-Center">' . $order['name'] . '</td>';
            echo '<td class="text-Center">' . $order['name_type'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['discount'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-Center">' . $order['delivery_date'] . '</td>';
            echo '<td class="text-Center">';
            if ($order['sales'] != "") {
                $employee = $dbc->GetRecord("os_users", "*", "id=" . $order['sales']);
                echo $employee['display'];
            } else {
                echo "-";
            }
            echo '</td>';
            echo '<td class="text-Center">' . $order['remove_reason'] . '</td>';
            echo '</tr>';

            $total_amount += $order['amount'];
            $total_vat += $order['discount'];
            $total_total += $order['total'];
            $total_net += $order['net'];
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
            <th class="text-center"></th>
            <th class="text-center"><?php echo number_format($total_vat, 2); ?></th>
            <th class="text-center"><?php echo number_format($total_total, 2); ?></th>
            <th class="text-center"><?php echo number_format($total_net, 2); ?></th>
            <th class="text-center" colspan="2"></th>
        </tr>
    </tfoot>
</table>