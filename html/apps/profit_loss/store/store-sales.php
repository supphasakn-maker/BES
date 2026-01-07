<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_orders_profit.id",
	"code" => "bs_orders_profit.code",
	"customer_id" => "bs_orders_profit.customer_id",
	"customer_name" => "bs_orders_profit.customer_name",
	"date" => "DATE(bs_orders_profit.date)",
	"sales" => "bs_orders_profit.sales",
	"user" => "bs_orders_profit.user",
	"type" => "bs_orders_profit.type",
	"parent" => "bs_orders_profit.parent",
	"is_split" => "bs_orders_profit.is_split",
	"split_sequence" => "bs_orders_profit.split_sequence",
	"created" => "bs_orders_profit.created",
	"updated" => "bs_orders_profit.updated",
	"amount" => "bs_orders_profit.amount",
	"price" => "FORMAT(bs_orders_profit.price,0)",
	"vat_type" => "bs_orders_profit.vat_type",
	"vat" => "bs_orders_profit.vat",
	"total" => "FORMAT(bs_orders_profit.total,0)",
	"net" => "bs_orders_profit.net",
	"delivery_date" => "bs_orders_profit.delivery_date",
	"delivery_time" => "bs_orders_profit.delivery_time",
	"lock_status" => "bs_orders_profit.lock_status",
	"status" => "bs_orders_profit.status",
	"comment" => "bs_orders_profit.comment",
	"shipping_address" => "bs_orders_profit.shipping_address",
	"billing_address" => "bs_orders_profit.billing_address",
	"rate_spot" => "bs_orders_profit.rate_spot",
	"rate_exchange" => "bs_orders_profit.rate_exchange",
	"billing_id" => "bs_orders_profit.billing_id",
	"currency" => "bs_orders_profit.currency",
	"info_payment" => "bs_orders_profit.info_payment",
	"info_contact" => "bs_orders_profit.info_contact",
	"delivery_id" => "bs_orders_profit.delivery_id",
	"remove_reason" => "bs_orders_profit.remove_reason",
	"product_id" => "bs_orders_profit.product_id",
	"order_id" => "bs_orders_profit.order_id",
	"mapping_true" => "bs_mapping_profit_orders.order_id",
	"mapping_id" => "bs_mapping_profit_orders.id",
	"mapping" => "bs_mapping_profit_orders.mapping_id",
	"mapping_true_usd" => "bs_mapping_profit_orders_usd.order_id",
	"mapping_id_usd" => "bs_mapping_profit_orders_usd.id",
	"mapping_usd" => "bs_mapping_profit_orders_usd.mapping_id",
);

$where = "bs_orders_profit.status > 0  
	AND YEAR(bs_orders_profit.date) > 2024
	AND bs_orders_profit.flag_hide = 0
	
	";
if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
	// แทนที่จะกรอง date ของ orders ให้กรอง mapped date แทน
	$where .= " AND (DATE(bs_orders_profit.date) = '" . $_GET['date_filter'] . "' 
	           OR EXISTS (
	               SELECT 1 FROM bs_mapping_profit_sumusd bms 
	               INNER JOIN bs_mapping_profit_orders_usd bmu ON bms.id = bmu.mapping_id 
	               WHERE bmu.order_id = bs_orders_profit.order_id 
	               AND DATE(bms.mapped) = '" . $_GET['date_filter'] . "'
	           )
	           OR EXISTS (
	               SELECT 1 FROM bs_mapping_profit bmp_main
	               INNER JOIN bs_mapping_profit_orders bmp_orders ON bmp_main.id = bmp_orders.mapping_id 
	               WHERE bmp_orders.order_id = bs_orders_profit.order_id 
	               AND DATE(bmp_main.mapped) = '" . $_GET['date_filter'] . "'
	           ))";
}

$table = array(
	"index" => "id",
	"name" => "bs_orders_profit",
	"join" => array(
		array(
			"field" => "order_id",
			"table" => "bs_mapping_profit_orders",
			"with" => "order_id",
		),
		array(
			"field" => "order_id", // bs_orders_profit.order_id
			"table" => "bs_mapping_profit_orders_usd",
			"with" => "order_id",
		)

	),
	"where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$data = $dbc->GetResult();

for ($i = 0; $i < count($data['aaData']); $i++) {
	$data['aaData'][$i]['DT_RowId'] = $data['aaData'][$i]['order_id'];
	$data['aaData'][$i]['id'] = $data['aaData'][$i]['order_id'];

	if ($data['aaData'][$i]['mapping_id'] != null) {
		$data['aaData'][$i]['mapping_item_id'] = $data['aaData'][$i]['mapping_id'];
	}
}

$sql = "SELECT SUM(bs_orders_profit.amount) AS amount , SUM(bs_orders_profit.total) AS total FROM bs_orders_profit 
	LEFT JOIN bs_mapping_profit_orders ON bs_mapping_profit_orders.order_id = bs_orders_profit.order_id 
	LEFT JOIN bs_mapping_profit_orders_usd ON bs_mapping_profit_orders_usd.order_id = bs_orders_profit.order_id 
	WHERE bs_mapping_profit_orders.id IS NULL AND bs_mapping_profit_orders_usd.id IS NULL
	AND bs_orders_profit.status > 0
	AND YEAR(bs_orders_profit.date) > 2024
	AND bs_orders_profit.flag_hide = 0
	";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
	$sql .= " AND DATE(bs_orders_profit.date) = '" . $_GET['date_filter'] . "'";
}

$rst = $dbc->Query($sql);
$line = $dbc->Fetch($rst);

$sql = "SELECT SUM(bs_orders_profit.amount) AS amount , SUM(bs_orders_profit.total) AS total FROM bs_orders_profit 
	LEFT JOIN bs_mapping_profit_orders ON bs_mapping_profit_orders.order_id = bs_orders_profit.order_id 
	WHERE bs_mapping_profit_orders.id IS NOT NULL 
	AND bs_orders_profit.status > 0
	AND YEAR(bs_orders_profit.date) > 2024
	AND bs_orders_profit.flag_hide = 0 
	";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
	$sql .= " AND DATE(bs_orders_profit.date) = '" . $_GET['date_filter'] . "'";
}

$rst = $dbc->Query($sql);
$line2 = $dbc->Fetch($rst);

$sql = "SELECT SUM(bs_orders_profit.amount) AS amount , SUM(bs_orders_profit.total) AS total FROM bs_orders_profit 
	LEFT JOIN bs_mapping_profit_orders_usd ON bs_mapping_profit_orders_usd.order_id = bs_orders_profit.order_id 
	WHERE bs_mapping_profit_orders_usd.id IS NOT NULL 
	AND bs_orders_profit.status > 0
	AND YEAR(bs_orders_profit.date) > 2024
	AND bs_orders_profit.flag_hide = 0 
	";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
	$sql .= " AND DATE(bs_orders_profit.date) = '" . $_GET['date_filter'] . "'";
}

$rst = $dbc->Query($sql);
$line3 = $dbc->Fetch($rst);

$data['total'] = array(
	"remain_unmatch" => $line[0],
	"remain_matching" => 0, // เซ็ตเป็น 0 เพราะไม่ได้ใช้แล้ว
	"remain_total" => $line[0],
	"remain_price" => $line[1],
	"remain_matchthbamount" => $line2[0],
	"remain_matchthbday" => $line2[1],
	"remain_matchusdamount" => $line3[0],
	"remain_matchusdday" => $line3[1]
);

echo json_encode($data);

$dbc->Close();
