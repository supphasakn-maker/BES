<?php
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

function summary_query($date, $filter)
{
    global $dbc;
    return $dbc->Query("
        SELECT
            o.product_type,
            COALESCE(pt.code, '-') AS type_code,
            COALESCE(pt.name, 'ไม่ระบุประเภท') AS type_name,
            SUM(o.amount) AS sum_amount,
            SUM(
                CASE 
                    WHEN o.product_id = 1 THEN o.amount * 0.015
                    WHEN o.product_id = 2 THEN o.amount * 0.050
                    WHEN o.product_id = 3 THEN o.amount * 0.150
                    ELSE 0
                END
            ) AS sum_kg,
            SUM(o.net) AS sum_net
        FROM bs_deliveries_bwd d
        JOIN bs_orders_bwd p
            ON p.delivery_id = d.id AND p.parent IS NULL
        JOIN bs_orders_bwd o
            ON (o.id = p.id OR o.parent = p.id) AND o.status > 0
        LEFT JOIN bs_products_type pt
            ON pt.id = o.product_type
        WHERE DATE(d.delivery_date) = '" . $dbc->Escape_String($date) . "'
          AND $filter
        GROUP BY o.product_type, pt.code, pt.name
        ORDER BY pt.name
    ");
}
?>
<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?></title>
    <style>
        body {
            font-family: system-ui, Segoe UI, Roboto, Arial, sans-serif;
            font-size: 14px;
            color: #222
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px
        }

        thead th {
            background: #111827;
            color: #fff
        }

        .right {
            text-align: right
        }

        .center {
            text-align: center
        }

        .muted {
            color: #6b7280
        }

        .table-primary {
            background: #e0f2fe
        }

        .table-section {
            background: #f1f5f9;
            font-weight: 700
        }

        h2 {
            margin: 20px 0 10px;
            font-size: 16px;
        }

        .summary-bar {
            background: #f9fafb;
            color: #374151;
        }

        .summary-bar td {
            font-size: 12px;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .summary-key {
            font-weight: 600;
            margin-left: 12px;
        }

        .summary-key:first-child {
            margin-left: 0;
        }
    </style>
</head>

<body>

    <?php
    $rst_special = summary_query($date, "o.product_id IN (1,2,3)");
    $sum_amount = $sum_kg = $sum_net = 0.0;

    ?>
    <table>
        <thead>
            <tr>
                <th>รายละเอียด</th>
                <th class="right">BARS รวม</th>
                <th class="right">KG รวม</th>
                <th class="right">NET รวม (THB)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pt_rows = [];
            if ($rst_special && $dbc->Total($rst_special) > 0) {
                while ($r = $dbc->Fetch($rst_special)) {
                    $pt_rows[] = $r;
                    $sum_amount += (float)$r['sum_amount'];
                    $sum_kg     += (float)$r['sum_kg'];
                    $sum_net    += (float)$r['sum_net'];
                    echo '<tr>';
                    echo '<td class="text-dark">' . htmlspecialchars($r['type_code'] . ' - ' . $r['type_name']) . '</td>';
                    echo '<td class="right text-dark">' . number_format((float)$r['sum_amount'], 4) . '</td>';
                    echo '<td class="right text-dark">' . number_format((float)$r['sum_kg'], 3) . '</td>';
                    echo '<td class="right text-dark">' . number_format((float)$r['sum_net'], 2) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="4" class="center muted">ไม่มีรายการสำหรับวันนี้</td></tr>';
            }
            ?>
        </tbody>
        <thead>
            <tr>
                <th class="right">รวมทั้งหมด</th>
                <th class="right"><?php echo number_format($sum_amount, 4); ?></th>
                <th class="right"><?php echo number_format($sum_kg, 3); ?></th>
                <th class="right"><?php echo number_format($sum_net, 2); ?></th>
            </tr>
        </thead>
    </table>

    <?php
    echo "<h2 class='text-dark fint-weight-bold'>สรุป กล่อง</h2>";

    $rst_other = summary_query($date, "o.product_id NOT IN (1,2,3)");
    $sum_amount = $sum_kg = $sum_net = 0.0;
    ?>
    <table>
        <thead>
            <tr>
                <th>รายละเอียด</th>
                <th class="right">กล่อง รวม</th>
                <th class="right">KG รวม</th>
                <th class="right">NET รวม (THB)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($rst_other && $dbc->Total($rst_other) > 0) {
                while ($r = $dbc->Fetch($rst_other)) {
                    $pt_rows[] = $r;
                    $sum_amount += (float)$r['sum_amount'];
                    $sum_kg     += (float)$r['sum_kg'];
                    $sum_net    += (float)$r['sum_net'];
                    echo '<tr>';
                    echo '<td class="text-dark">' . htmlspecialchars($r['type_code'] . ' - ' . $r['type_name']) . '</td>';
                    echo '<td class="right text-dark">' . number_format((float)$r['sum_amount'], 4) . '</td>';
                    echo '<td class="right text-dark">' . number_format((float)$r['sum_kg'], 3) . '</td>';
                    echo '<td class="right text-dark">' . number_format((float)$r['sum_net'], 2) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="4" class="center muted">ไม่มีรายการสำหรับวันนี้</td></tr>';
            }
            ?>
        </tbody>
        <thead>
            <tr>
                <th class="right ">รวมทั้งหมด</th>
                <th class="right"><?php echo number_format($sum_amount, 4); ?></th>
                <th class="right"><?php echo number_format($sum_kg, 3); ?></th>
                <th class="right"><?php echo number_format($sum_net, 2); ?></th>
            </tr>
        </thead>
    </table>


    <table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
        <thead>
            <tr>
                <th class="text-center table-dark font-weight-bold" colspan="10">
                    ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?>
                </th>
            </tr>
            <tr>
                <th class="text-center table-dark font-weight-bold">DATE</th>
                <th class="text-center table-dark font-weight-bold">BILL NO.</th>
                <th class="text-center table-dark font-weight-bold">ORDER NO.</th>
                <th class="text-center table-dark font-weight-bold">CUSTOMER</th>
                <th class="text-center table-dark font-weight-bold">BARS</th>
                <th class="text-center table-dark font-weight-bold">BATH / KGS.</th>
                <th class="text-center table-dark font-weight-bold">TOTAL - DISCOUNT</th>
                <th class="text-center table-dark font-weight-bold">PERIOD</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pt_rows as $pt) {
                $type_id   = is_null($pt['product_type']) ? 'NULL' : (int)$pt['product_type'];
                $type_code = $pt['type_code'];
                $type_name = $pt['type_name'];

                echo '<tr class="table-section">';
                echo '<td colspan="8">' . htmlspecialchars($type_code . ' - ' . $type_name) . '</td>';
                echo '</tr>';

                $sql_orders_by_type = "
                SELECT
                    parent.id AS parent_id,
                    ANY_VALUE(parent.date)          AS date,
                    ANY_VALUE(d.delivery_date)      AS delivery_date,
                    ANY_VALUE(d.billing_id)         AS code,
                    ANY_VALUE(parent.customer_name) AS customer_name,
                    ANY_VALUE(parent.code)          AS order_number,
                    ANY_VALUE(parent.delivery_time) AS delivery_time,
                    SUM(o.amount) AS sum_amount,
                    SUM(o.total)  AS sum_total,
                    SUM(o.net)    AS sum_net,
                    SUM(
                        CASE 
                            WHEN o.product_id = 1 THEN o.amount * 0.015
                            WHEN o.product_id = 2 THEN o.amount * 0.050
                            WHEN o.product_id = 3 THEN o.amount * 0.150
                            ELSE 0
                        END
                    ) AS sum_kg,
                    CASE 
                        WHEN SUM(o.amount) > 0 THEN SUM(o.price * o.amount) / SUM(o.amount)
                        ELSE 0
                    END AS wavg_price
                FROM bs_deliveries_bwd d
                JOIN bs_orders_bwd parent
                    ON parent.delivery_id = d.id AND parent.parent IS NULL
                JOIN bs_orders_bwd o
                    ON (o.id = parent.id OR o.parent = parent.id) AND o.status > 0
                WHERE DATE(d.delivery_date) = '" . $dbc->Escape_String($date) . "'
                  AND " . ($type_id === 'NULL' ? "o.product_type IS NULL" : "o.product_type = " . $dbc->Escape_String($type_id)) . "
                GROUP BY parent.id
                ORDER BY parent.date ASC, parent.id ASC
            ";
                $rst_orders = $dbc->Query($sql_orders_by_type);

                if ($rst_orders && $dbc->Total($rst_orders) > 0) {
                    $type_total_amount = 0.0;
                    $type_total_kg     = 0.0;
                    $type_total_net    = 0.0;

                    while ($row = $dbc->Fetch($rst_orders)) {
                        $type_total_amount += (float)$row['sum_amount'];
                        $type_total_kg     += (float)$row['sum_kg'];
                        $type_total_net    += (float)$row['sum_net'];

                        echo '<tr class="table-primary">';
                        echo '<td>' . date("d/m/Y", strtotime($row['date'])) . '</td>';
                        echo '<td>' . htmlspecialchars($row['code']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['order_number']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
                        echo '<td class="text-right">' . number_format((float)$row['sum_amount'], 4) . '</td>';
                        echo '<td class="text-right">' . number_format((float)$row['wavg_price'], 2) . ' / ' . number_format((float)$row['sum_kg'], 3) . '</td>';
                        echo '<td class="text-right">' . number_format((float)$row['sum_net'], 2) . '</td>';
                        echo '<td>' . htmlspecialchars($row['delivery_time']) . '</td>';
                        echo '</tr>';
                    }

                    echo '<tr class="table-section">';
                    echo '<th colspan="4" class="right">รวม ' . htmlspecialchars($type_name) . '</th>';
                    echo '<th class="right">' . number_format($type_total_amount, 4) . '</th>';
                    echo '<th class="right">KG: ' . number_format($type_total_kg, 3) . '</th>';
                    echo '<th class="right">' . number_format($type_total_net, 2) . '</th>';
                    echo '<th></th>';
                    echo '</tr>';
                } else {
                    echo '<tr><td colspan="8" class="center muted">ไม่มีออเดอร์สำหรับประเภทนี้</td></tr>';
                }
            }
            ?>
        </tbody>
    </table>
</body>

</html>