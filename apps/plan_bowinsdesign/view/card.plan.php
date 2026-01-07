<?php
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

/**
 * ดึง note รวมรายวัน (เผื่อใช้เป็น fallback ถ้า comment ของแถวว่าง)
 */
$sql_notes = "
    SELECT
        DATE(submited) AS received_date,
        GROUP_CONCAT(DISTINCT NULLIF(TRIM(comment),'') ORDER BY id SEPARATOR ' | ') AS comments
    FROM bs_stock_bwd
    WHERE status = 0
      AND submited IS NOT NULL
    GROUP BY DATE(submited)
";
$notes_map = [];
$rst_notes = $dbc->Query($sql_notes);
while ($r = $dbc->Fetch($rst_notes)) {
    $notes_map[$r['received_date']] = $r['comments'] ?? '';
}

/**
 * ปรับหลัก: เลิก GROUP BY เพื่อ “แยกแต่ละการรับเข้า” ออกมาเป็นคนละบรรทัด
 * คำนวณน้ำหนักต่อแถวจาก product_id * amount แล้วตั้งชื่อ weight_kg
 */
$sql = "
    SELECT
        s.id,
        DATE(s.submited) AS received_date,
        s.amount,                -- จำนวนที่รับเข้าในรายการนี้
        s.product_id,
        p.name AS product_name,
        s.product_type,
        pt.name AS type_name,
        NULLIF(TRIM(s.comment), '') AS row_comment,
        (
            CASE 
                WHEN s.product_id = 1 THEN COALESCE(s.amount,0) * 0.015
                WHEN s.product_id = 2 THEN COALESCE(s.amount,0) * 0.050
                WHEN s.product_id = 3 THEN COALESCE(s.amount,0) * 0.150
                ELSE 0
            END
        ) AS weight_kg
    FROM bs_stock_bwd s
    LEFT JOIN bs_products_bwd  p  ON p.id  = s.product_id
    LEFT JOIN bs_products_type pt ON pt.id = s.product_type
    WHERE s.status = 0
      AND s.submited IS NOT NULL
    ORDER BY received_date DESC, product_name ASC, type_name ASC, s.id ASC
";
$rst = $dbc->Query($sql);

$current_date = null;
$daily_weight = 0.0;
$daily_items  = 0;
$daily_amount = 0.0; // เก็บสำรองไว้หากต้องการใช้ภายหลัง

$grand_weight = 0.0;
$grand_items  = 0;
$grand_amount = 0.0;

ob_start();
while ($row = $dbc->Fetch($rst)) {
    $rec_date     = $row['received_date'];
    $product_name = $row['product_name'] ?? '';
    $type_name    = $row['type_name'] ?? '';

    // สำหรับโหมด “แยกรายการรับเข้า” จำนวน = amount ของแถวนั้น
    $items        = (int)num($row['amount'] ?? 0);
    $tot_weight   = num($row['weight_kg'] ?? 0);

    // ใช้คอมเมนต์ของแถวก่อน ถ้าไม่มีให้ fallback เป็นโน้ตรวมของวัน
    $row_comment = trim((string)$row['row_comment']);
    $note_text   = ($row_comment !== '') ? $row_comment : trim($notes_map[$rec_date] ?? '');
    $note_short  = ($note_text !== '') ? mb_strimwidth($note_text, 0, 120, '…', 'UTF-8') : '-';

    // เปลี่ยนวัน = ปิดสรุปของวันก่อน แล้วเปิดส่วนหัววันใหม่
    if ($current_date !== $rec_date) {
        if ($current_date !== null) {
            echo '<tr class="table-secondary">
                    <td class="text-center"><b>สรุป</b></td>
                    <td class="text-center"><b>' . number_format($daily_weight, 2) . '</b></td>
                    <td class="text-right" colspan="4"><b>รายการ: ' . number_format($daily_items) . '</b></td>
                  </tr>';
        }

        echo '<tr class="bg-light">
                <td colspan="6"><b>วันที่รับเข้า: ' . h($rec_date) . '</b></td>
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
            <td class="text-left" style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="' . h($type_name) . '">' . h($type_name) . '</td>
            <td class="text-center">' . h($rec_date) . '</td>
            <td class="text-left text-dark fw-bold" style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="' . h($note_text) . '">' . h($note_short) . '</td>
          </tr>';

    // สะสมต่อวัน / รวมทั้งหมด
    $daily_weight += $tot_weight;
    $daily_items  += $items;

    $grand_weight += $tot_weight;
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
                    <th class="text-center text-white font-weight">หมายเหตุ</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($tbodyHtml) {
                    echo $tbodyHtml;
                    if ($current_date !== null) {
                        echo '<tr class="table-secondary">
                                <td class="text-center"><b>สรุปทั้งวัน</b></td>
                                <td class="text-center"><b>' . number_format($daily_weight, 2) . '</b></td>
                                <td class="text-right" colspan="4"><b>รายการ: ' . number_format($daily_items) . '</b></td>
                              </tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center text-muted">ไม่พบข้อมูล</td></tr>';
                }
                ?>
            </tbody>

            <tfoot>
                <tr class="table-secondary">
                    <th class="text-center">ยอดรวมทั้งหมด</th>
                    <th class="text-center"><?= number_format($grand_weight, 3) ?></th>
                    <th class="text-right" colspan="4">
                        รายการทั้งหมด: <?= number_format($grand_items) ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>