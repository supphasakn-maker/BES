<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_purchase_spot.id",
    "supplier_id" => "bs_purchase_spot.supplier_id",
    "supplier" => "bs_suppliers.name",
    "type" => "bs_purchase_spot.type",
    "amount" => "bs_purchase_spot.amount",
    "rate_spot" => "bs_purchase_spot.rate_spot",
    "rate_pmdc" => "bs_purchase_spot.rate_pmdc",
    "total" => "FORMAT(bs_purchase_spot.amount*32.1507*(bs_purchase_spot.rate_spot+bs_purchase_spot.rate_pmdc),2)",
    "date" => "bs_purchase_spot.date",
    "value_date" => "bs_purchase_spot.value_date",
    "created" => "bs_purchase_spot.created",
    "updated" => "bs_purchase_spot.updated",
    "method" => "bs_purchase_spot.method",
    "ref" => "bs_purchase_spot.ref",
    "user" => "bs_purchase_spot.user",
    "status" => "bs_purchase_spot.status",
    "comment" => "bs_purchase_spot.comment",
    "confirm" => "bs_purchase_spot.confirm",
    "order_id" => "bs_purchase_spot.order_id",
    "trade_id" => "bs_purchase_spot.trade_id",
    "import_id" => "bs_purchase_spot.import_id",
    "parent" => "bs_purchase_spot.parent",
    "transfer_id" => "bs_purchase_spot.transfer_id",
    "adjust_id" => "bs_purchase_spot.adjust_id",
    "adjust_type" => "bs_purchase_spot.adjust_type"
);

$where = "bs_purchase_spot.parent IS NULL
			AND flag_hide = 0
			AND bs_purchase_spot.rate_spot > 0
			AND (bs_purchase_spot.type LIKE 'physical'
			OR bs_purchase_spot.type LIKE 'stock') 
			AND (bs_purchase_spot.status > 0 OR bs_purchase_spot.status = -1)
			AND YEAR(date) > 2024";

if (isset($_GET['date_filter'])) {
    $where .= " AND bs_purchase_spot.date = '" . $_GET['date_filter'] . "'";
}

$table = array(
    "index" => "id",
    "name" => "bs_purchase_spot",
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
$sql = "SELECT SUM(bs_purchase_spot.amount*32.1507*(bs_purchase_spot.rate_spot+bs_purchase_spot.rate_pmdc)) AS total , SUM(bs_purchase_spot.amount) AS amount FROM bs_purchase_spot 
WHERE (bs_purchase_spot.type LIKE 'physical'
OR bs_purchase_spot.type LIKE 'stock'
) AND bs_purchase_spot.status > 0
AND flag_hide = 0 AND YEAR(date) > 2024";

if (isset($_GET['date_filter'])) {
    $sql .= " AND bs_purchase_spot.date = '" . $_GET['date_filter'] . "'";
}

$rst = $dbc->Query($sql);
$line = $dbc->Fetch($rst);

$data['total'] = array(
    "remian_unmatch" => $line[0],
    "remain_matching" => $total_remain,
    "remain_total" => $line[0] + $total_remain,
    "remain_amount" => $line[1] + $total_remain
);


echo json_encode($data);

$dbc->Close();
