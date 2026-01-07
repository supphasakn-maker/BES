<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap text-success">
    <thead class="bg-success">
        <tr>
            <th class="text-center font-weight-bold text-dark" colspan="13">ภาพรวมการรับซื้อแท่งเงินคืนประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?></th>
        </tr>
        <tr>
            <th class="text-center font-weight-bold text-dark">PO</th>
            <th class="text-center font-weight-bold text-dark">CUSTOMER</th>
            <th class="text-center font-weight-bold text-dark">KIO</th>
            <th class="text-center font-weight-bold text-dark">BATH / KGS.</th>
            <th class="text-center font-weight-bold text-dark">VAT</th>
            <th class="text-center font-weight-bold text-dark">TOTAL</th>
            <th class="text-center font-weight-bold text-dark">TOTAL + VAT</th>
            <th class="text-center font-weight-bold text-dark">DATE</th>
            <th class="text-center font-weight-bold text-dark">SALE</th>
            <th class="text-center font-weight-bold text-dark">SP</th>
            <th class="text-center font-weight-bold text-dark">EX</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_amount = 0;
        $total_vat = 0;
        $total_total = 0;
        $total_net = 0;
        $counter = 0;

        $sql = "SELECT * FROM bs_orders_buy WHERE DATE(bs_orders_buy.created) LIKE '" . $date . "'";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-Center">' . $order['code'] . '</td>';
            echo '<td class="text-Center">';
            if ($order['customer_id'] != "") {
                $employee = $dbc->GetRecord("bs_customers", "*", "id=" . $order['customer_id']);
                echo $employee['name'];
            } else {
                echo "-";
            }
            echo '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['vat'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';

            echo '<td class="text-Center">';
            echo $order['created'];

            echo '</td>';
            echo '<td class="text-Center">';
            if ($order['sales'] != "") {
                $employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
                echo $employee['fullname'];
            } else {
                echo "-";
            }
            echo '</td>';
            echo '<td class="text-right">' . number_format($order['rate_spot'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['rate_exchange'], 2) . '</td>';
            echo '</tr>';

            $total_amount += $order['amount'];
            $total_vat += $order['vat'];
            $total_total += $order['total'];
            $total_net += $order['net'];
            $counter++;
        }




        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="2">
                จากเอกสารทั้งหมด <?php echo $counter; ?> รายการ
            </th>
            <th class="text-center"><?php echo number_format($total_amount, 4); ?></th>
            <th class="text-center"></th>
            <th class="text-center"><?php echo number_format($total_vat, 2); ?></th>
            <th class="text-center"><?php echo number_format($total_total, 2); ?></th>
            <th class="text-center"><?php echo number_format($total_net, 2); ?></th>
            <th class="text-center" colspan="1"></th>
        </tr>
    </tfoot>
</table>