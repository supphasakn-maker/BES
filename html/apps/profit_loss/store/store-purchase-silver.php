<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_purchase_spot_profit.id",
    "supplier_id" => "bs_purchase_spot_profit.supplier_id",
    "supplier" => "bs_suppliers.name",
    "type" => "bs_purchase_spot_profit.type",
    "amount" => "bs_purchase_spot_profit.amount",
    "rate_spot" => "bs_purchase_spot_profit.rate_spot",
    "rate_pmdc" => "bs_purchase_spot_profit.rate_pmdc",
    "total" => "(bs_purchase_spot_profit.amount*32.1507*(bs_purchase_spot_profit.rate_spot+bs_purchase_spot_profit.rate_pmdc))",
    "date" => "bs_purchase_spot_profit.date",
    "value_date" => "bs_purchase_spot_profit.value_date",
    "created" => "bs_purchase_spot_profit.created",
    "updated" => "bs_purchase_spot_profit.updated",
    "method" => "bs_purchase_spot_profit.method",
    "ref" => "bs_purchase_spot_profit.ref",
    "user" => "bs_purchase_spot_profit.user",
    "status" => "bs_purchase_spot_profit.status",
    "comment" => "bs_purchase_spot_profit.comment",
    "confirm" => "bs_purchase_spot_profit.confirm",
    "order_id" => "bs_purchase_spot_profit.order_id",
    "trade_id" => "bs_purchase_spot_profit.trade_id",
    "import_id" => "bs_purchase_spot_profit.import_id",
    "currency" => "bs_purchase_spot_profit.currency",
    "parent" => "bs_purchase_spot_profit.parent",
    "THBValue" => "bs_purchase_spot_profit.THBValue",
    "transfer_id" => "bs_purchase_spot_profit.transfer_id",
    "adjust_id" => "bs_purchase_spot_profit.adjust_id",
    "adjust_type" => "bs_purchase_spot_profit.adjust_type"
);

$where = "bs_purchase_spot_profit.parent IS NULL 
			AND flag_hide = 0
			AND bs_purchase_spot_profit.rate_spot > 0
			AND (bs_purchase_spot_profit.type LIKE 'physical'
			OR bs_purchase_spot_profit.type LIKE 'MTM') 
			AND (bs_purchase_spot_profit.status > 0 OR bs_purchase_spot_profit.status = -1)
			AND YEAR(date) > 2024";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
    $where .= " AND (bs_purchase_spot_profit.value_date = '" . $_GET['date_filter'] . "' )";
}

$table = array(
    "index" => "id",
    "name" => "bs_purchase_spot_profit",
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
$sql = "SELECT SUM(bs_purchase_spot_profit.amount*32.1507*(bs_purchase_spot_profit.rate_spot+bs_purchase_spot_profit.rate_pmdc))AS total , SUM(bs_purchase_spot_profit.amount) AS amount FROM bs_purchase_spot_profit 
WHERE (bs_purchase_spot_profit.type LIKE 'physical'
OR bs_purchase_spot_profit.type LIKE 'MTM'
) AND bs_purchase_spot_profit.status > 0 AND bs_purchase_spot_profit.currency = 'USD'
AND flag_hide = 0 AND YEAR(date) > 2024";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
    $sql .= " AND (bs_purchase_spot_profit.value_date = '" .  $_GET['date_filter'] . "')";
}

$rst = $dbc->Query($sql);
$line = $dbc->Fetch($rst);

$sql = "SELECT SUM(bs_purchase_spot_profit.THBValue)AS total , SUM(bs_purchase_spot_profit.amount) AS amount FROM bs_purchase_spot_profit 
WHERE (bs_purchase_spot_profit.type LIKE 'physical'
OR bs_purchase_spot_profit.type LIKE 'MTM'
) AND bs_purchase_spot_profit.status > 0 AND bs_purchase_spot_profit.currency = 'THB'
AND flag_hide = 0 AND YEAR(date) > 2024";

if (isset($_GET['date_filter'])) {
    $sql .= " AND (bs_purchase_spot_profit.value_date = '" .  $_GET['date_filter'] . "')";
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
