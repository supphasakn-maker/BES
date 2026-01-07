<?php
@ini_set('display_errors', 0);
@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', 300);
date_default_timezone_set('Asia/Bangkok');

require_once "../../../config/define.php";
require_once "../../../include/db.php";

$dbc = new dbc;
$dbc->Connect();

$templatePath = __DIR__ . "/templates/29.10.68.xlsx";
$sheetIndex   = 0;
$sender = [
    'name'  => "นายณัฐวุฒิ",
    'phone' => "0922729328",
    'addr'  => "74/1 ซอยสุขาภิบาล 2 ซอย 31 แขวงดอกไม้ เขตประเวศ กรุงเทพ",
    'zip'   => "10250",
];
$serviceCode = 'E';

$insurance_table = [
    2500,
    3000,
    3500,
    4000,
    4500,
    5000,
    5500,
    6000,
    6500,
    7000,
    7500,
    8000,
    8500,
    9000,
    9500,
    10000,
    10500,
    11000,
    11500,
    12000,
    12500,
    13000,
    13500,
    14000,
    14500,
    15000,
    15500,
    16000,
    16500,
    17000,
    17500,
    18000,
    18500,
    19000,
    19500,
    20000,
    20500,
    21000,
    21500,
    22000,
    22500,
    23000,
    23500,
    24000,
    24500,
    25000,
    25500,
    26000,
    26500,
    27000,
    27500,
    28000,
    28500,
    29000,
    29500,
    30000,
    30500,
    31000,
    31500,
    32000,
    32500,
    33000,
    33500,
    34000,
    34500,
    35000,
    35500,
    36000,
    36500,
    37000,
    37500,
    38000,
    38500,
    39000,
    39500,
    40000,
    40500,
    41000,
    41500,
    42000,
    42500,
    43000,
    43500,
    44000,
    44500,
    45000,
    45500,
    46000,
    46500,
    47000,
    47500,
    48000,
    48500,
    49000,
    49500,
    50000
];

function cleanText($s)
{
    if ($s === null) return '';
    $s = trim((string)$s);
    $s = preg_replace("/[\r\n]+/u", " ", $s);
    $s = preg_replace('/\s{2,}/u', ' ', $s);
    return $s;
}

function normalize_phone($s)
{
    if ($s === null) return '';
    $s = trim((string)$s);
    $s = str_replace([' ', '-', '(', ')'], '', $s);
    if (preg_match('/^\+?66(\d{8,9})$/', $s, $m)) return '0' . $m[1];
    return $s;
}

function extract_zip($addrRaw)
{
    if ($addrRaw === null) return '';
    $raw = trim((string)$addrRaw);
    if (preg_match('/(\d{5})(?!.*\d)/u', $raw, $m)) return $m[1];
    return '';
}

function calculate_insurance($box_total, $insurance_table)
{
    foreach ($insurance_table as $coverage) {
        if ($box_total <= $coverage) {
            return $coverage;
        }
    }
    return end($insurance_table);
}

function xlsx_env_check($templatePath)
{
    $checks = [];
    $autoload = __DIR__ . "/../../../vendor/autoload.php";
    $checks['autoload'] = [file_exists($autoload), $autoload];

    $checks['template_exists'] = [file_exists($templatePath), $templatePath];
    $checks['ext_zip'] = [extension_loaded('zip'), 'zip'];
    $checks['ext_xml'] = [extension_loaded('xml'), 'xml'];

    $classOk = false;
    if ($checks['autoload'][0]) {
        require_once $autoload;
        $classOk = class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');
    }
    $checks['phpspreadsheet'] = [$classOk, 'PhpSpreadsheet'];

    foreach ($checks as $c) {
        if (!$c[0]) return [false, $checks];
    }
    return [true, $checks];
}

if (isset($_GET['excel']) && $_GET['excel'] === 'check') {
    [$ok, $checks] = xlsx_env_check($templatePath);
    header('Content-Type: text/plain; charset=utf-8');
    echo "[Excel Check]\n";
    foreach ($checks as $k => $v) {
        echo ($v[0] ? "OK " : "FAIL ") . " - $k : " . $v[1] . "\n";
    }
    echo "\nResult: " . ($ok ? "READY ✅" : "NOT READY ❌");
    exit;
}

$ids = isset($_POST['ids']) ? (array)$_POST['ids'] : [];
$ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
if (empty($ids)) {
    die("No order IDs");
}

$in = implode(',', $ids);

$sql_parent = "
    SELECT id, code, customer_name, phone, shipping_address
    FROM bs_orders_bwd
    WHERE id IN ($in) AND (parent IS NULL OR parent = 0)
    ORDER BY FIELD(id, $in)
";
$rs_parent = $dbc->Query($sql_parent);
$parent_orders = [];
while ($row = $dbc->Fetch($rs_parent)) {
    $parent_orders[$row['id']] = $row;
}

$parent_ids = array_keys($parent_orders);
if (empty($parent_ids)) {
    die("No valid parent orders found");
}
$parent_in = implode(',', $parent_ids);

$sql_boxes = "
    SELECT 
        COALESCE(parent, id) AS parent_id,
        box_number,
        customer_name,
        phone,
        shipping_address,
        SUM(net) AS box_total
    FROM bs_orders_bwd
    WHERE (id IN ($parent_in) OR parent IN ($parent_in))
    GROUP BY COALESCE(parent, id), box_number
    ORDER BY COALESCE(parent, id), box_number
";
$rs_boxes = $dbc->Query($sql_boxes);

$boxes_by_parent = [];
while ($box = $dbc->Fetch($rs_boxes)) {
    $pid = (int)$box['parent_id'];
    if (!isset($boxes_by_parent[$pid])) {
        $boxes_by_parent[$pid] = [];
    }
    $boxes_by_parent[$pid][] = $box;
}

$rows = [];
foreach ($parent_orders as $pid => $parent) {
    if (!isset($boxes_by_parent[$pid])) continue;

    $boxes = $boxes_by_parent[$pid];
    $total_boxes = count($boxes);

    foreach ($boxes as $idx => $box) {
        $box_number = (int)$box['box_number'];
        $box_total = (float)$box['box_total'];

        $insurance = calculate_insurance($box_total, $insurance_table);

        $order_code = cleanText($parent['code']);
        if ($total_boxes > 1) {
            $order_code .= "-" . ($box_number + 1);
        }
        $order_code .= " รับประกัน " . number_format($box_total, 0) . " บาท";

        $rows[] = [
            'sender_name' => $sender['name'],
            'sender_phone' => $sender['phone'],
            'sender_addr' => $sender['addr'],
            'sender_zip' => $sender['zip'],
            'service' => $serviceCode,
            'order_code' => $order_code,
            'customer_name' => cleanText($box['customer_name']),
            'phone' => normalize_phone($box['phone']),
            'recv_addr' => cleanText($box['shipping_address']),
            'zip' => extract_zip($box['shipping_address']),
        ];
    }
}

[$canXlsx,] = xlsx_env_check($templatePath);
if (!$canXlsx) {
    header('Content-Type:text/plain; charset=UTF-8');
    die("ERROR: ระบบไม่สามารถสร้างไฟล์ Excel ได้\nโปรดรัน: script.php?excel=check เพื่อดูรายละเอียด");
}

require_once __DIR__ . "/../../../vendor/autoload.php";
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($templatePath);
$sheet = $spreadsheet->getSheet($sheetIndex);

$headerRow = 2;
$startRow = $headerRow + 1;
$r = $startRow;

foreach ($rows as $row) {
    $sheet->setCellValue("A{$r}", $row['sender_name']);
    $sheet->setCellValue("B{$r}", $row['sender_phone']);
    $sheet->setCellValue("C{$r}", $row['sender_addr']);
    $sheet->setCellValue("D{$r}", $row['sender_zip']);
    $sheet->setCellValue("E{$r}", $row['service']);
    $sheet->setCellValue("I{$r}", $row['order_code']);
    $sheet->setCellValue("J{$r}", $row['customer_name']);
    $sheet->setCellValue("K{$r}", $row['phone']);
    $sheet->setCellValue("L{$r}", $row['recv_addr']);
    $sheet->setCellValue("M{$r}", $row['zip']);
    $r++;
}

$filename = "thaipost_export_" . date('Ymd_His') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('php://output');
exit;
