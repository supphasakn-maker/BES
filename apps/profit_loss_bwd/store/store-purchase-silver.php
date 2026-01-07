<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_purchase_spot_profit_bwd.id",
    "supplier_id" => "bs_purchase_spot_profit_bwd.supplier_id",
    "supplier" => "bs_suppliers.name",
    "type" => "bs_purchase_spot_profit_bwd.type",
    "amount" => "bs_purchase_spot_profit_bwd.amount",
    "rate_spot" => "bs_purchase_spot_profit_bwd.rate_spot",
    "rate_pmdc" => "bs_purchase_spot_profit_bwd.rate_pmdc",
    "total" => "(bs_purchase_spot_profit_bwd.amount*32.1507*(bs_purchase_spot_profit_bwd.rate_spot+bs_purchase_spot_profit_bwd.rate_pmdc))",
    "date" => "bs_purchase_spot_profit_bwd.date",
    "value_date" => "bs_purchase_spot_profit_bwd.value_date",
    "created" => "bs_purchase_spot_profit_bwd.created",
    "updated" => "bs_purchase_spot_profit_bwd.updated",
    "method" => "bs_purchase_spot_profit_bwd.method",
    "ref" => "bs_purchase_spot_profit_bwd.ref",
    "user" => "bs_purchase_spot_profit_bwd.user",
    "status" => "bs_purchase_spot_profit_bwd.status",
    "comment" => "bs_purchase_spot_profit_bwd.comment",
    "confirm" => "bs_purchase_spot_profit_bwd.confirm",
    "order_id" => "bs_purchase_spot_profit_bwd.order_id",
    "trade_id" => "bs_purchase_spot_profit_bwd.trade_id",
    "import_id" => "bs_purchase_spot_profit_bwd.import_id",
    "currency" => "bs_purchase_spot_profit_bwd.currency",
    "parent" => "bs_purchase_spot_profit_bwd.parent",
    "THBValue" => "bs_purchase_spot_profit_bwd.THBValue",
    "transfer_id" => "bs_purchase_spot_profit_bwd.transfer_id",
    "adjust_id" => "bs_purchase_spot_profit_bwd.adjust_id",
    "adjust_type" => "bs_purchase_spot_profit_bwd.adjust_type"
);

$where = "bs_purchase_spot_profit_bwd.parent IS NULL 
			AND flag_hide = 0
			AND bs_purchase_spot_profit_bwd.rate_spot > 0
			AND (
				bs_purchase_spot_profit_bwd.ref = 'BWD'
				OR (bs_purchase_spot_profit_bwd.currency = 'THB' AND bs_purchase_spot_profit_bwd.supplier_id = '28')
				OR (bs_purchase_spot_profit_bwd.currency = 'USD' AND bs_purchase_spot_profit_bwd.ref = 'BWD')
			)
			AND (bs_purchase_spot_profit_bwd.type LIKE 'physical'
			OR bs_purchase_spot_profit_bwd.type LIKE 'MTM') 
			AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
			AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
    $where .= " AND (bs_purchase_spot_profit_bwd.value_date = '" . $_GET['date_filter'] . "' )";
}

$table = array(
    "index" => "id",
    "name" => "bs_purchase_spot_profit_bwd",
    "join" => array(
        array(
            "field" => "supplier_id",
            "table" => "bs_suppliers",
            "with" => "id"
        )
    ),
    "where" => $where
);


$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$total_remain = 0;

$aaData = array();
$data = $dbc->GetResult();
$sql = "SELECT SUM(bs_purchase_spot_profit_bwd.amount*32.1507*(bs_purchase_spot_profit_bwd.rate_spot+bs_purchase_spot_profit_bwd.rate_pmdc))AS total , SUM(bs_purchase_spot_profit_bwd.amount) AS amount FROM bs_purchase_spot_profit_bwd 
WHERE (bs_purchase_spot_profit_bwd.type LIKE 'physical'
OR bs_purchase_spot_profit_bwd.type LIKE 'MTM'
) AND bs_purchase_spot_profit_bwd.status > 0 AND bs_purchase_spot_profit_bwd.currency = 'USD'
AND bs_purchase_spot_profit_bwd.ref = 'BWD'
AND flag_hide = 0 AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
    $sql .= " AND (bs_purchase_spot_profit_bwd.value_date = '" .  $_GET['date_filter'] . "')";
}

$rst = $dbc->Query($sql);
$line = $dbc->Fetch($rst);

$sql = "SELECT SUM(bs_purchase_spot_profit_bwd.THBValue)AS total , SUM(bs_purchase_spot_profit_bwd.amount) AS amount FROM bs_purchase_spot_profit_bwd 
WHERE (bs_purchase_spot_profit_bwd.type LIKE 'physical'
OR bs_purchase_spot_profit_bwd.type LIKE 'MTM'
) AND bs_purchase_spot_profit_bwd.status > 0 AND bs_purchase_spot_profit_bwd.currency = 'THB'
AND bs_purchase_spot_profit_bwd.supplier_id = '28'
AND bs_purchase_spot_profit_bwd.ref LIKE '%แท่งดี%'
AND flag_hide = 0 AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'";

if (isset($_GET['date_filter'])) {
    $sql .= " AND (bs_purchase_spot_profit_bwd.value_date = '" .  $_GET['date_filter'] . "')";
}

$rst = $dbc->Query($sql);
$line2 = $dbc->Fetch($rst);

$data['total'] = array(
    "remian_unmatch" => $line[0],
    "remain_matching" => $total_remain,
    "remain_total" => $line[0] + $total_remain,
    "remain_amount" => $line[1] + $total_remain,
    "remain_total_thb" => $line2[0] + $total_remain,
    "remain_amount_thb" => $line2[1] + $total_remain
);


echo json_encode($data);

$dbc->Close();
