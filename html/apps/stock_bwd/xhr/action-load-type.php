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

$products = $dbc->GetRecord("bs_products_type", "*", "id=" . $_POST['product_type']);
$next_start = "";
$next_number_only = "";

if ($products) {
	$product_type_id = $_POST['product_type'] ?? null;
	$product_id = $products['product_id'] ?? null;

	$sql = "SELECT code FROM bs_stock_bwd 
        WHERE product_type = '{$product_type_id}' 
          AND product_id = '{$product_id}' 
          AND code LIKE '%{$products['code']}%' 
        ORDER BY id DESC LIMIT 1";

	$result = $dbc->query($sql);

	if ($result && $result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$last_code = $row['code'];

		if (preg_match('/^(.*?)(\d+)$/', $last_code, $matches)) {
			$number_part = intval($matches[2]);
			$next_number_only = str_pad($number_part + 1, strlen($matches[2]), '0', STR_PAD_LEFT);
		}
	} else {
		$next_start = $products['code'] . "001";
	}
}

echo json_encode(array(
	'products' => $products,
	'next_start' => $next_number_only ?: $next_start // ใช้ค่า fallback หาก `$next_number_only` ว่าง
));



$dbc->Close();
