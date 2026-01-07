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
    'name'  => "นายอานัส",
    'phone' => "0922729328",
    'addr'  => "74/1 ซอยสุขาภิบาล 2 ซอย 31 แขวงดอกไม้ เขตประเวศ กรุงเทพ",
    'zip'   => "10250",
];
$serviceCode = 'E'; // Thaipost service code

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

function xlsx_env_check($templatePath)
{
    $checks = [];
    $autoload = __DIR__ . "/../../../vendor/autoload.php";
    $checks['autoload']        = [file_exists($autoload), $autoload];
    $checks['template_exists'] = [file_exists($templatePath), $templatePath];
    $checks['ext_zip']         = [extension_loaded('zip'), 'zip'];
    $checks['ext_xml']         = [extension_loaded('xml'), 'xml'];
    $checks['ext_mbstring']    = [extension_loaded('mbstring'), 'mbstring'];

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
    header('Content-Type: text/plain; charset=UTF-8');
    echo "[Excel Check]\n";
    foreach ($checks as $k => $v) echo ($v[0] ? "OK  " : "FAIL ") . " - $k : " . $v[1] . "\n";
    echo "\nResult: " . ($ok ? "READY ✅" : "NOT READY ❌");
    exit;
}
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
if ($debug) {
    @ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$ids = isset($_POST['ids']) ? (array)$_POST['ids'] : [];
$ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
if (empty($ids)) {
    header('Content-Type: text/plain; charset=UTF-8');
    exit("No order IDs");
}
$in = implode(',', $ids);


$sql = "
SELECT
  o.id,
  o.code AS order_code,
  c.contact AS customer_name,
  COALESCE(NULLIF(c.phone,''), c.phone) AS phone,
  c.shipping_address
FROM bs_orders o
LEFT JOIN bs_customers c ON c.id = o.customer_id
WHERE o.id IN ($in)
ORDER BY FIELD(o.id, $in)
";

$rs = $dbc->Query($sql);
if ($rs === false) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo "SQL failed.\n";
    if ($debug) echo "Query: $sql\n";
    exit;
}

$rows = [];
while ($row = $dbc->Fetch($rs)) {
    $order_code = cleanText($row['order_code'] ?? '');
    $cust_name  = cleanText($row['customer_name'] ?? '');
    $phone      = normalize_phone($row['phone'] ?? '');
    $addrRaw    = $row['shipping_address'] ?? '';
    $addr       = cleanText($addrRaw);
    $zip        = extract_zip($addrRaw);

    $rows[] = [
        'sender_name'   => $sender['name'],
        'sender_phone'  => $sender['phone'],
        'sender_addr'   => $sender['addr'],
        'sender_zip'    => $sender['zip'],
        'service'       => $serviceCode,
        'order_code'    => $order_code,
        'customer_name' => $cust_name,
        'phone'         => $phone,
        'recv_addr'     => $addr,
        'zip'           => $zip,
    ];
}

[$canXlsx, $checks] = xlsx_env_check($templatePath);
if (!$canXlsx) {
    header('Content-Type:text/plain; charset=UTF-8');
    foreach ($checks as $k => $v) if (!$v[0]) echo "- Missing: $k ($v[1])\n";
    exit;
}

require_once __DIR__ . "/../../../vendor/autoload.php";
try {
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($templatePath);
    $sheet = $spreadsheet->getSheet($sheetIndex);

    $r = 3;
    foreach ($rows as $row) {
        $sheet->setCellValue("A{$r}", $row['sender_name']);
        $sheet->setCellValueExplicit("B{$r}", $row['sender_phone'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue("C{$r}", $row['sender_addr']);
        $sheet->setCellValue("D{$r}", $row['sender_zip']);
        $sheet->setCellValue("E{$r}", $row['service']);

        $sheet->setCellValue("F{$r}", '');
        $sheet->setCellValue("G{$r}", '');
        $sheet->setCellValue("H{$r}", '');

        $sheet->setCellValue("I{$r}", $row['order_code']);                                        // รายการ/หมายเหตุ
        $sheet->setCellValue("J{$r}", $row['customer_name']);
        $sheet->setCellValueExplicit("K{$r}", $row['phone'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue("L{$r}", $row['recv_addr']);
        $sheet->setCellValueExplicit("M{$r}", $row['zip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $r++;
    }

    $filename = "thaipost_export_silver_" . date('Ymd_His') . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (\Throwable $e) {
    if ($debug) {
        header('Content-Type: text/plain; charset=UTF-8');
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    exit;
}
