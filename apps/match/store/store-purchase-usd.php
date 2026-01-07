<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_purchase_usd.id",
	"bank" => "bs_purchase_usd.bank",
	"type" => "bs_purchase_usd.type",
	"amount" => "FORMAT(bs_purchase_usd.amount,2)",
	"rate_exchange" => "bs_purchase_usd.rate_exchange",
	"rate_finance" => "bs_purchase_usd.rate_finance",
	"date" => "bs_purchase_usd.date",
	"comment" => "bs_purchase_usd.comment",
	"method" => "bs_purchase_usd.method",
	"ref" => "bs_purchase_usd.ref",
	"user" => "bs_purchase_usd.user",
	"status" => "bs_purchase_usd.status",
	"confirm" => "bs_purchase_usd.confirm",
	"created" => "bs_purchase_usd.created",
	"updated" => "bs_purchase_usd.updated",
	"parent" => "bs_purchase_usd.parent",
	"bank_date" => "bs_purchase_usd.bank_date",
	"premium_start" => "bs_purchase_usd.premium_start",
	"premium" => "bs_purchase_usd.premium",
	"transfer_id" => "bs_purchase_usd.transfer_id",
	"fw_contract_no" => "bs_purchase_usd.fw_contract_no",
	"unpaid" => "bs_purchase_usd.unpaid",
	"value" => "FORMAT(bs_purchase_usd.amount*bs_purchase_usd.rate_finance,2)"
);






$where = "
    bs_mapping_usd_purchases.id IS NULL
    AND bs_purchase_usd.status <> -1
    AND bs_purchase_usd.type LIKE 'physical'
    AND YEAR(bs_purchase_usd.date) > 2024"; // เพิ่ม bs_purchase_usd. เพื่อความชัดเจน

if (isset($_GET['date_filter'])) {
	$where .= " AND bs_purchase_usd.date = '" . $_GET['date_filter'] . "'";
}

$table = array(
	"index" => "id",
	"name" => "bs_purchase_usd",
	"join" => array(
		array(
			"field" => "id",
			"table" => "bs_mapping_usd_purchases",
			"with" => "purchase_id",
			"type" => "LEFT" // ใช้ LEFT JOIN เพื่อให้รายการจาก bs_purchase_usd ยังคงแสดงแม้จะไม่มีการ map
		)
	),
	"where" => $where
);


$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$data = $dbc->GetResult(); // ดึงผลลัพธ์หลัก




$total_remain_matching_amount = 0;
$mapped_unmatched_items = array();



$sql_mapped_unmatched = "SELECT * FROM bs_mapping_usd_purchases WHERE mapping_id IS NULL";
$rst_mapped = $dbc->Query($sql_mapped_unmatched);

while ($mapping = $dbc->Fetch($rst_mapped)) {
	$purchase = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $mapping['purchase_id']);


	if ($purchase) {
		$value = $purchase['amount'] * $purchase['rate_finance'];


		array_push($mapped_unmatched_items, array(
			"DT_RowId" => $purchase['id'],
			"id" => $purchase['id'],
			"bank" => $purchase['bank'],
			"type" => $purchase['type'],
			"amount" => number_format($purchase['amount'], 2),
			"rate_exchange" => $purchase['rate_exchange'],
			"rate_finance" => $purchase['rate_finance'],
			"total" => number_format($value, 2),
			"date" => $purchase['date'],
			"remain" => number_format($mapping['amount'], 2), // แสดงยอด remain จาก mapping table
			"mapping_item_id" => $mapping['id'],
			"value" => number_format($value, 2), // ใช้ $value ที่คำนวณไว้แล้ว
		));

		$total_remain_matching_amount += $mapping['amount'];
	}
}


$final_aaData = array_merge($data['aaData'], $mapped_unmatched_items);
$data['aaData'] = $final_aaData;





$sql_unmatch_sum = "SELECT SUM(bs_purchase_usd.amount) FROM bs_purchase_usd 
    LEFT JOIN bs_mapping_usd_purchases ON bs_mapping_usd_purchases.purchase_id = bs_purchase_usd.id 
    WHERE " . $where; // ใช้ $where ที่กรอง mapped items ออกไปแล้ว

$rst_unmatch_sum = $dbc->Query($sql_unmatch_sum);
$line_unmatch_sum = $dbc->Fetch($rst_unmatch_sum);
$remian_unmatch = $line_unmatch_sum[0] ? $line_unmatch_sum[0] : 0; // ตรวจสอบค่า null


$remain_total = $remian_unmatch + $total_remain_matching_amount;




$sql_unmatch_thb_sum = "SELECT SUM(bs_purchase_usd.amount * bs_purchase_usd.rate_finance) FROM bs_purchase_usd 
    LEFT JOIN bs_mapping_usd_purchases ON bs_mapping_usd_purchases.purchase_id = bs_purchase_usd.id 
    WHERE " . $where;

$rst_unmatch_thb_sum = $dbc->Query($sql_unmatch_thb_sum);
$line_unmatch_thb_sum = $dbc->Fetch($rst_unmatch_thb_sum);
$remian_unmatch_thb = $line_unmatch_thb_sum[0] ? $line_unmatch_thb_sum[0] : 0;



$total_remain_matching_thb = 0;
foreach ($mapped_unmatched_items as $item) {

	$total_remain_matching_thb += (float)str_replace(',', '', $item['value']);
}
$remain_total_thb = $remian_unmatch_thb + $total_remain_matching_thb;


$data['total'] = array(
	"remian_unmatch" => $remian_unmatch,
	"remain_matching" => $total_remain_matching_amount,
	"remain_total" => $remain_total,
	"remain_total_thb" => $remain_total_thb // เพิ่ม THB รวม
);


$data['recordsTotal'] = count($final_aaData);
$data['recordsFiltered'] = count($final_aaData);


echo json_encode($data);

$dbc->Close();
