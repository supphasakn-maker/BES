<?php


/* ---------- Helper ---------- */
function num($v)
{
    if ($v === null) return 0.0;
    if (is_string($v)) {
        $v = trim($v);
        if ($v === '') return 0.0;
        $v = str_replace([',', ' '], '', $v);
    }
    return (float)$v;
}
function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}


$sql = "
    SELECT
        DATE(s.submited) AS received_date,
        s.product_id,
        p.name AS product_name,
        s.product_type,
        pt.name AS type_name,
        COUNT(*) AS items,
        SUM(COALESCE(s.amount, 0)) AS total_amount,
        SUM(
            CASE 
                WHEN s.product_id = 1 THEN s.amount * 0.015
                WHEN s.product_id = 2 THEN s.amount * 0.050
                WHEN s.product_id = 3 THEN s.amount * 0.150
                ELSE 0
            END
        ) AS total_weight
    FROM bs_stock_bwd s
    LEFT JOIN bs_products_bwd  p  ON p.id  = s.product_id
    LEFT JOIN bs_products_type pt ON pt.id = s.product_type
    WHERE s.status = 0
      AND s.submited IS NOT NULL
    GROUP BY DATE(s.submited), s.product_id, s.product_type
    ORDER BY received_date DESC, product_name ASC, type_name ASC
";

$rst = $dbc->Query($sql);

$current_date = null;
$daily_weight = 0.0;
$daily_items  = 0;
$daily_amount = 0.0;

$grand_weight = 0.0;
$grand_items  = 0;
$grand_amount = 0.0;

ob_start();
while ($row = $dbc->Fetch($rst)) {
    $rec_date     = $row['received_date'];
    $product_name = $row['product_name'] ?? '';
    $type_name    = $row['type_name'] ?? '';

    $items        = (int)num($row['items'] ?? 0);
    $tot_amount   = num($row['total_amount'] ?? 0);
    $tot_weight   = num($row['total_weight'] ?? 0);
    if ($current_date !== $rec_date) {
        if ($current_date !== null) {
            echo '<tr class="table-secondary">
                    <td class="text-center"><b>สรุป</b></td>
                    <td class="text-center"><b>' . number_format($daily_weight, 0) . '</b></td>
                    <td class="text-right" colspan="3"><b>รายการ: ' . number_format($daily_items) . '</b></td>
                    <td></td>
                  </tr>';
        }

        echo '<tr class="bg-light">
                <td colspan="9"><b>วันที่รับเข้า: ' . h($rec_date) . '</b></td>
              </tr>';

        $current_date = $rec_date;
        $daily_weight = 0.0;
        $daily_items  = 0;
        $daily_amount = 0.0;
    }

    echo '<tr>
            <td class="text-center">' . number_format($items, 0) . '</td>
            <td class="text-center">' . number_format($tot_weight, 3) . '</td>
            <td class="text-left">' . h($product_name) . '</td>
            <td class="text-left">' . h($type_name) . ' (' . number_format($items) . ' รายการ)</td>
            <td class="text-center">' . h($rec_date) . '</td>
            <td></td>
          </tr>';

    $daily_weight += $tot_weight;
    $daily_amount += $tot_amount;
    $daily_items  += $items;

    $grand_weight += $tot_weight;
    $grand_amount += $tot_amount;
    $grand_items  += $items;
}
$tbodyHtml = ob_get_clean();
?>

<div class="card mb-2">
    <div class="card-body">
        <table class="table table-striped table-bordered table-hover table-middle" width="100%">
            <thead class="bg-dark">
                <tr>
                    <th class="text-center text-white font-weight">จำนวน</th>
                    <th class="text-center text-white font-weight">น้ำหนัก (กก.)</th>
                    <th class="text-center text-white font-weight">Product</th>
                    <th class="text-center text-white font-weight">Type</th>
                    <th class="text-center text-white font-weight">วันที่รับเข้า</th>
                    <th class="text-center text-white font-weight"></th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($tbodyHtml) {
                    echo $tbodyHtml;
                    if ($current_date !== null) {
                        echo '<tr class="table-secondary">
                         <td class="text-center"><b>สรุปทั้งวัน</b></b></td>
                          <td class="text-center"><b>' . number_format($daily_weight, 2) . '</b></td>
                          <td class="text-right" colspan="4"><b>รายการ: ' . number_format($daily_items) . '</b></td>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center text-muted">ไม่พบข้อมูล</td></tr>';
                }
                ?>
            </tbody>

            <tfoot>
                <tr class="table-secondary">
                    <th class="text-center">ยอดรวมทั้งหมด</th>
                    <th class="text-center"><?= number_format($grand_weight, 3) ?></th>
                    <th class="text-right" colspan="5">
                        รายการทั้งหมด: <?= number_format($grand_items) ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>