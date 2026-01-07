<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

function json_exit($ok, $msg, $extra = [])
{
    echo json_encode(array_merge(['success' => $ok, 'msg' => $msg], $extra));
    exit();
}

function calculateValueDate($selectedDate)
{
    $timestamp = strtotime($selectedDate);
    if ($timestamp === false) {
        return $selectedDate;
    }
    $dayOfWeek = date("l", $timestamp);
    if (in_array($dayOfWeek, ["Thursday", "Friday"])) {
        return date("Y-m-d", strtotime("+4 day", $timestamp));
    }
    return date("Y-m-d", strtotime("+2 day", $timestamp));
}

if (empty($_SESSION['auth']['user_id'])) {
    json_exit(false, 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
}

$supplier_id = $_POST['supplier_id'] ?? "";
$product_id  = $_POST['product_id'] ?? "";
$type        = $_POST['type'] ?? "";
$amount      = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$date        = $_POST['date'] ?? "";
$method      = $_POST['method'] ?? "";
$ref         = $_POST['ref'] ?? '';
$comment     = isset($_POST['comment']) ? addslashes($_POST['comment']) : "";
$rate_spot   = isset($_POST['rate_spot']) ? floatval($_POST['rate_spot']) : 0;

$value_date_input = $_POST['value_date'] ?? $date;
$date2 = calculateValueDate($value_date_input);

if ($supplier_id === "") json_exit(false, 'โปรดระบุ supplier');
if ($product_id === "")  json_exit(false, 'โปรดระบุ product');
if ($amount <= 0)        json_exit(false, 'โปรดระบุราคาจำนวนกิโล');
if ($date === "")        json_exit(false, 'โปรดระบุวันที่');

$data_buyfix_primary = [
    '#id'         => "DEFAULT",
    "supplier_id" => $supplier_id,
    "type"        => $type,
    "#amount"     => $amount,
    "#ounces"     => $amount * 32.1507,
    "date"        => $date,
    "method"      => $method,
    "#user"       => $os->auth['id'],
    "#product_id" => (int)$product_id,
    "#status"     => 1,
    '#created'    => 'NOW()',
    '#updated'    => 'NOW()'
];

$data_sales_spot = [
    '#id'         => "DEFAULT",
    "type"        => 'Physical',
    "#supplier_id" => 1,
    "#amount"     => $amount,
    "#rate_spot"  => 0,
    "#rate_pmdc"  => 0,
    "date"        => $date,
    "value_date"  => $date2,
    '#created'    => 'NOW()',
    '#updated'    => 'NOW()',
    "method"      => 'Via Message',
    "maturity"    => 'Today',
    "ref"         => $ref,
    "#user"       => $os->auth['id'],
    "#status"     => 1,
    "comment"     => $comment,
    "#trade_id"   => null,
    "#product_id" => (int)$product_id
];

$data_buyfix_sell = [
    '#id'         => "DEFAULT",
    "#supplier_id" => 1,
    "type"        => "Sell",
    "#amount"     => $amount,
    "#ounces"     => $amount * 32.1507,
    "date"        => $date,
    "method"      => 'Today',
    "#user"       => $os->auth['id'],
    "#product_id" => (int)$product_id,
    "#status"     => 1,
    "#created"    => 'NOW()',
    "#updated"    => 'NOW()'
];

$data_smg_sales = [
    '#id'           => "DEFAULT",
    "date"          => $date2,
    "type"          => $type,
    "purchase_type" => 'Sell',
    "#amount"       => $amount,
    "#rate_spot"    => $rate_spot,
    "#rate_pmdc"    => 0
];

try {
    if (!$dbc->Insert("bs_purchase_buyfix", $data_buyfix_primary)) {
        throw new Exception("Insert bs_purchase_buyfix (primary) failed.");
    }
    $spot_id = $dbc->GetID();

    if (!$dbc->Insert("bs_sales_spot", $data_sales_spot)) {
        throw new Exception("Insert bs_sales_spot failed.");
    }
    $sales_spot_id = $dbc->GetID();

    if (!$dbc->Insert("bs_purchase_buyfix", $data_buyfix_sell)) {
        throw new Exception("Insert bs_purchase_buyfix (sell) failed.");
    }
    $buyfix_sell_id = $dbc->GetID();

    $dbc->Update("bs_purchase_buyfix", ["sales_spot" => $sales_spot_id], "id=" . intval($buyfix_sell_id));

    if (!$dbc->Insert("bs_smg_trade", $data_smg_sales)) {
        throw new Exception("Insert bs_smg_trade (sales) failed.");
    }
    $smg_sales_id = $dbc->GetID();

    $dbc->Update("bs_smg_trade", ["purchase_spot" => $sales_spot_id], "id=" . intval($smg_sales_id));

    $os->save_log(0, $_SESSION['auth']['user_id'], "buy_fixed-add", $spot_id, ["buy_fixed" => $spot_id]);

    json_exit(true, "บันทึกสำเร็จ", [
        'spot_id'        => $spot_id,
        'sales_spot_id'  => $sales_spot_id,
        'buyfix_sell_id' => $buyfix_sell_id,
        'smg_sales_id'   => $smg_sales_id,
        'value_date'     => $date2
    ]);
} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage());
    json_exit(false, $e->getMessage());
} finally {
    $dbc->Close();
}
