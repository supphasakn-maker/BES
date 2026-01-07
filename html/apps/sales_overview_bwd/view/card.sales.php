<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
    <thead class="bg-dark">
        <tr>
            <!-- เดิม colspan=14 เพิ่มคอลัมน์ KGS. = รวมเป็น 15 -->
            <th class="text-center text-white font-weight-bold" colspan="16">
                ภาพรวมการขายประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?>
            </th>
        </tr>
        <tr>
            <th class="text-center text-white font-weight-bold">PO</th>
            <th class="text-center text-white font-weight-bold">CUSTOMER</th>
            <th class="text-center text-white font-weight-bold">PLATFORM</th>
            <th class="text-center text-white font-weight-bold">PRODUCT</th>
            <th class="text-center text-white font-weight-bold">PRODUCT TYPE</th>
            <th class="text-center text-white font-weight-bold">BARS</th>
            <th class="text-center text-white font-weight-bold">DATE PURCHASE</th>
            <th class="text-center text-white font-weight-bold">KGS.</th>
            <th class="text-center text-white font-weight-bold">BATH / KGS.</th>
            <th class="text-center text-white font-weight-bold">DISCOUNT</th>
            <th class="text-center text-white font-weight-bold">FEE</th>
            <th class="text-center text-white font-weight-bold">TOTAL</th>
            <th class="text-center text-white font-weight-bold">TOTAL - DISCOUNT</th>
            <th class="text-center text-white font-weight-bold">DELIVERY DATE</th>
            <th class="text-center text-white font-weight-bold">SALE</th>
            <th class="text-center text-white font-weight-bold"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_amount = 0;
        $total_fee = 0;
        $total_total = 0;
        $total_net = 0;
        $total_discount = 0;
        $total_kg = 0;
        $counter = 0;

        $sql = "SELECT  
                parent.id, parent.sales, parent.code, parent.customer_name, parent.product_id,
                SUM(o.amount) AS amount, 
                SUM(o.price)  AS price, 
                SUM(o.discount) AS discount, 
                SUM(o.fee)    AS fee, 
                SUM(o.net)    AS net,
                SUM(o.total)  AS total, 
                parent.delivery_date, 
                o.platform, 
                o.date, 
                p.name, 
                pt.name AS name_type,
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
                LEFT OUTER JOIN bs_products_bwd p 
                    ON parent.product_id = p.id
                LEFT OUTER JOIN bs_products_type pt 
                    ON parent.product_type = pt.id
                WHERE DATE(parent.created) = '" . $dbc->Escape_String($date) . "'
                AND parent.parent IS NULL
                AND o.status > 0
                GROUP BY parent.id";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-left">' . $order['code'] . '</td>';
            echo '<td class="text-left">' . $order['customer_name'] . '</td>';
            echo '<td class="text-left">' . $order['platform'] . '</td>';
            echo '<td class="text-center">' . $order['name'] . '</td>';
            echo '<td class="text-center">' . $order['name_type'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . date("d/m/Y", strtotime($order['date'])) . '</td>';

            // ✅ แสดงกิโลที่คำนวณแล้ว
            echo '<td class="text-right">' . number_format($order['weight_kg'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['discount'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['fee'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-left">' . $order['delivery_date'] . '</td>';
            echo '<td class="text-left">';
            if ($order['sales'] != "") {
                $employee = $dbc->GetRecord("os_users", "*", "id=" . intval($order['sales']));
                echo $employee ? $employee['display'] : "-";
            } else {
                echo "-";
            }
            echo '</td>';
            echo '<td class="text-center">';
            echo '<button onclick="fn.app.sales_screen_bwd_2.multiorder.dialog_remove_each(' . intval($order['id']) . ')" class="btn btn-xs btn-outline-danger mr-1 btn-icon"><i class="fa fa-trash"></i></button>';
            echo '</td>';
            echo '</tr>';

            // ✅ สะสมยอดรวม
            $total_amount   += (float)$order['amount'];
            $total_discount += (float)$order['discount'];
            $total_total    += (float)$order['total'];
            $total_net      += (float)$order['net'];
            $total_fee      += (float)$order['fee'];
            $total_kg       += (float)$order['weight_kg'];   // รวมกิโล
            $counter++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="5">
                จากเอกสารทั้งหมด <?php echo $counter; ?> รายการ
            </th>
            <th class="text-center"><?php echo number_format($total_amount, 4); ?></th>
            <th class="text-center"></th>
            <th class="text-right"><?php echo number_format($total_kg, 4); ?></th>
            <th class="text-center"></th>
            <th class="text-right"><?php echo number_format($total_discount, 2); ?></th>
            <th class="text-right"><?php echo number_format($total_fee, 2); ?></th>
            <th class="text-right"><?php echo number_format($total_total, 2); ?></th>
            <th class="text-right"><?php echo number_format($total_net, 2); ?></th>
            <th class="text-center" colspan="2"></th>
        </tr>
    </tfoot>
</table>