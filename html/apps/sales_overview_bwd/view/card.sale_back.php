<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap text-success">
    <thead class="bg-success">
        <tr>
            <th class="text-center text-white font-weight-bold" colspan="12">รายการที่ขายคืน <?php echo date("d/m/Y", strtotime($date)); ?></th>
        </tr>
        <tr>
            <th class="text-center text-white font-weight-bold">PO</th>
            <th class="text-center text-white font-weight-bold">CUSTOMER</th>
            <th class="text-center text-white font-weight-bold">PRODUCT</th>
            <th class="text-center text-white font-weight-bold">PRODUCT TYPE</th>
            <th class="text-center text-white font-weight-bold">BARS</th>
            <th class="text-center text-white font-weight-bold">BATH / KGS.</th>
            <th class="text-center text-white font-weight-bold">TOTAL</th>
            <th class="text-center text-white font-weight-bold">DATE</th>
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

        $sql = "SELECT  bs_orders_back_bwd.id,bs_orders_back_bwd.sales, bs_orders_back_bwd.code, bs_orders_back_bwd.customer_name,bs_orders_back_bwd.product_id,
        bs_orders_back_bwd.amount ,bs_orders_back_bwd.price,bs_orders_back_bwd.total,bs_orders_back_bwd.date,bs_products_bwd.name ,bs_products_type.name AS name_type
        FROM bs_orders_back_bwd 
        LEFT OUTER JOIN bs_products_bwd ON bs_orders_back_bwd.product_id = bs_products_bwd.id
		LEFT OUTER JOIN bs_products_type ON bs_orders_back_bwd.product_type = bs_products_type.id
        WHERE DATE(date) LIKE '" . $date . "' ";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-Center">' . $order['code'] . '</td>';
            echo '<td class="text-Center">' . $order['customer_name'] . '</td>';
            echo '<td class="text-Center">' . $order['name'] . '</td>';
            echo '<td class="text-Center">' . $order['name_type'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-Center">' . $order['date'] . '</td>';
            echo '<td class="text-Center">';
            if ($order['sales'] != "") {
                $employee = $dbc->GetRecord("os_users", "*", "id=" . $order['sales']);
                echo $employee['display'];
            } else {
                echo "-";
            }
            echo '</td>';
            echo '</tr>';

            $total_amount += $order['amount'];
            $total_total += $order['total'];
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
            <th class="text-center"><?php echo number_format($total_total, 2); ?></th>
            <th class="text-center" colspan="3"></th>
        </tr>
    </tfoot>
</table>