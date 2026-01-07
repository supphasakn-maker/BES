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





$where = " bs_purchase_usd.status <> -1 AND bs_purchase_usd.comment = 'BWD' AND (bs_purchase_usd.type LIKE 'physical') AND bs_purchase_usd.date >= '2025-10-01'";


if (isset($_GET['date_filter'])) {
    $where .= " AND bs_purchase_usd.date = '" . $_GET['date_filter'] . "'";
}

$table = array(
    "index" => "id",
    "name" => "bs_purchase_usd",
    "where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$total_remain = 0;

$aaData = array();
$data = $dbc->GetResult();

$sql = "SELECT SUM(bs_purchase_usd.amount) FROM bs_purchase_usd 
    WHERE " . $where;

$rst = $dbc->Query($sql);
$line = $dbc->Fetch($rst);

$data['total'] = array(
    "remian_unmatch" => $line[0],
    "remain_matching" => $total_remain,
    "remain_total" => $line[0] + $total_remain
);


echo json_encode($data);

$dbc->Close();
