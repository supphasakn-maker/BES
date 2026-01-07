<?php
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$start_date    = "2023-12-31";
$start_balance = 0;
$start_net     = 0;

$sql = "
    SELECT
        SUM(o.amount) AS amount,
        SUM(CASE 
                WHEN o.product_id = 1 THEN o.amount * 0.015
                WHEN o.product_id = 2 THEN o.amount * 0.050
                WHEN o.product_id = 3 THEN o.amount * 0.150
                ELSE 0
            END) AS kg,
        SUM(o.total) AS total,
        SUM(o.net)   AS net,
        COUNT(DISTINCT parent.id) AS total_order
    FROM bs_orders_bwd parent
    LEFT JOIN bs_orders_bwd o
           ON (o.id = parent.id OR o.parent = parent.id)
    WHERE DATE(parent.date) = '" . $dbc->Escape_String($date) . "'
      AND parent.parent IS NULL
      AND o.status > 0
    AND o.product_id IN (1,2,3)
";
$rst   = $dbc->Query($sql);
$total = $dbc->Fetch($rst);

$sql = "
    SELECT
        SUM(o.amount) AS amount,
        SUM(CASE 
                WHEN o.product_id = 1 THEN o.amount * 0.015
                WHEN o.product_id = 2 THEN o.amount * 0.050
                WHEN o.product_id = 3 THEN o.amount * 0.150
                ELSE 0
            END) AS kg,
        SUM(o.total) AS total,
        SUM(o.net)   AS net,
        COUNT(DISTINCT parent.id) AS total_order
    FROM bs_deliveries_bwd d
    LEFT JOIN bs_orders_bwd parent
           ON d.id = parent.delivery_id
          AND parent.parent IS NULL
    LEFT JOIN bs_orders_bwd o
           ON (o.id = parent.id OR o.parent = parent.id)
    WHERE DATE(d.delivery_date) = '" . $dbc->Escape_String($date) . "'
      AND o.status > 0
      AND o.product_id IN (1,2,3)
";
$rst      = $dbc->Query($sql);
$delivery = $dbc->Fetch($rst);

$sql = "
    SELECT
        SUM(b.amount) AS amount,
        SUM(CASE 
                WHEN b.product_id = 1 THEN b.amount * 0.015
                WHEN b.product_id = 2 THEN b.amount * 0.050
                WHEN b.product_id = 3 THEN b.amount * 0.150
                ELSE 0
            END) AS kg,
        SUM(b.total) AS total,
        SUM(b.net)   AS net,
        COUNT(DISTINCT IF(b.parent IS NULL, b.id, b.parent)) AS total_order
    FROM bs_orders_bwd b
    WHERE DATE(b.date) <  '" . $dbc->Escape_String($date) . "'
      AND DATE(b.date) >= '" . $dbc->Escape_String($start_date) . "'
      AND b.status > 0
       AND b.product_id IN (1,2,3)
";
$rst     = $dbc->Query($sql);
$balance = $dbc->Fetch($rst);
$balance['amount'] += $start_balance;
$balance['net']    += $start_net;

$sql = "
    SELECT
        SUM(o.amount) AS amount,
        SUM(CASE 
                WHEN o.product_id = 1 THEN o.amount * 0.015
                WHEN o.product_id = 2 THEN o.amount * 0.050
                WHEN o.product_id = 3 THEN o.amount * 0.150
                ELSE 0
            END) AS kg,
        SUM(o.total) AS total,
        SUM(o.net)   AS net,
        COUNT(DISTINCT parent.id) AS total_order
    FROM bs_deliveries_bwd d
    LEFT JOIN bs_orders_bwd parent
           ON d.id = parent.delivery_id
          AND parent.parent IS NULL   
    LEFT JOIN bs_orders_bwd o
           ON (o.id = parent.id OR o.parent = parent.id)
    WHERE DATE(d.delivery_date) <  '" . $dbc->Escape_String($date) . "'
      AND DATE(d.delivery_date) >= '" . $dbc->Escape_String($start_date) . "'
      AND o.status > 0
      AND o.product_id IN (1,2,3)
";
$rst              = $dbc->Query($sql);
$balance_delivery = $dbc->Fetch($rst);

$balance['amount'] -= $balance_delivery['amount'];
$balance['kg']     -= $balance_delivery['kg'];
$balance['net']    -= $balance_delivery['net'];

?>

<div class="row gutters-sm">
    <div class="col-xl-4">
        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <tbody>
                <tr>
                    <th class="text-center table-dark font-weight-bold">TODAY BAR.</th>
                </tr>
                <tr>
                    <td class="text-center"><?php echo number_format((float)$total['amount'], 4); ?></td>
                </tr>

                <tr>
                    <th class="text-center table-dark font-weight-bold">TODAY KG.</th>
                </tr>
                <tr>
                    <td class="text-center"><?php echo number_format((float)$total['kg'], 4); ?></td>
                </tr>

                <tr>
                    <th class="text-center table-dark font-weight-bold">ORDER NO.</th>
                </tr>
                <tr>
                    <td class="text-center"><?php echo (int)$total['total_order']; ?></td>
                </tr>
            </tbody>
        </table>

        <button onclick="fn.dialog.open('apps/sales_overview_bwd/view/dialog.lock.lookup.php','#dialog_lock_lookup')"
            type="button" class="btn btn-warning">Lock Report</button>
    </div>

    <div class="col-xl-8">
        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <tbody>
                <tr>
                    <th class="text-left table-dark font-weight-bold">B/F</th>
                    <td class="text-center"><?php echo number_format((float)$balance['amount'], 4); ?></td>
                    <td class="text-center"><?php echo number_format((float)$balance['kg'], 4); ?></td>
                    <td class="text-center"><?php echo number_format((float)$balance['net'], 2); ?></td>
                </tr>
                <tr>
                    <th class="text-left table-dark font-weight-bold">TODAY SALE</th>
                    <td class="text-center"><?php echo number_format((float)$total['amount'], 4); ?></td>
                    <td class="text-center"><?php echo number_format((float)$total['kg'], 4); ?></td>
                    <td class="text-center"><?php echo number_format((float)$total['net'], 2); ?></td>
                </tr>
                <tr>
                    <th class="text-left table-dark font-weight-bold">TODAY DELIVERY</th>
                    <td class="text-center text-danger"><?php echo number_format(-(float)$delivery['amount'], 4); ?></td>
                    <td class="text-center text-danger"><?php echo number_format(-(float)$delivery['kg'], 4); ?></td>
                    <td class="text-center text-danger"><?php echo number_format(-(float)$delivery['net'], 2); ?></td>
                </tr>
                <tr>
                    <th class="text-left table-dark font-weight-bold">ADJUST</th>
                    <td class="text-center">0.0000</td>
                    <td class="text-center">0.0000</td>
                    <td class="text-center">0.00</td>
                </tr>
                <?php
                $balance['amount'] += $total['amount'] - $delivery['amount'];
                $balance['kg']     += $total['kg']     - $delivery['kg'];
                $balance['net']    += $total['net']    - $delivery['net'];
                ?>
                <tr>
                    <th class="text-left table-dark font-weight-bold">C/F</th>
                    <td class="text-center"><?php echo number_format((float)$balance['amount'], 4); ?></td>
                    <td class="text-center"><?php echo number_format((float)$balance['kg'], 4); ?></td>
                    <td class="text-center"><?php echo number_format((float)$balance['net'], 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>