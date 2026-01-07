<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_purchase_usd_profit_bwd.id",
    "bank" => "bs_purchase_usd_profit_bwd.bank",
    "type" => "bs_purchase_usd_profit_bwd.type",
    "amount" => "FORMAT(bs_purchase_usd_profit_bwd.amount,2)",
    "rate_exchange" => "bs_purchase_usd_profit_bwd.rate_exchange",
    "rate_finance" => "bs_purchase_usd_profit_bwd.rate_finance",
    "date" => "bs_purchase_usd_profit_bwd.date",
    "comment" => "bs_purchase_usd_profit_bwd.comment",
    "method" => "bs_purchase_usd_profit_bwd.method",
    "ref" => "bs_purchase_usd_profit_bwd.ref",
    "user" => "bs_purchase_usd_profit_bwd.user",
    "status" => "bs_purchase_usd_profit_bwd.status",
    "confirm" => "bs_purchase_usd_profit_bwd.confirm",
    "created" => "bs_purchase_usd_profit_bwd.created",
    "updated" => "bs_purchase_usd_profit_bwd.updated",
    "parent" => "bs_purchase_usd_profit_bwd.parent",
    "bank_date" => "bs_purchase_usd_profit_bwd.bank_date",
    "premium_start" => "bs_purchase_usd_profit_bwd.premium_start",
    "premium" => "bs_purchase_usd_profit_bwd.premium",
    "transfer_id" => "bs_purchase_usd_profit_bwd.transfer_id",
    "fw_contract_no" => "bs_purchase_usd_profit_bwd.fw_contract_no",
    "unpaid" => "bs_purchase_usd_profit_bwd.unpaid",
    "value" => "FORMAT(bs_purchase_usd_profit_bwd.amount*bs_purchase_usd_profit_bwd.rate_finance,2)"
);


$where = " bs_purchase_usd_profit_bwd.status <> -1 AND bs_purchase_usd_profit_bwd.comment = 'BWD' AND (bs_purchase_usd_profit_bwd.type LIKE 'physical' OR bs_purchase_usd_profit_bwd.type LIKE 'MTM')
AND bs_purchase_usd_profit_bwd.date >= '2025-10-01'";


if (isset($_GET['date_filter'])) {
    $where .= " AND (bs_purchase_usd_profit_bwd.value_date = '" .  $_GET['date_filter'] . "')";
}

$table = array(
    "index" => "id",
    "name" => "bs_purchase_usd_profit_bwd",
    "where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$total_remain = 0;

$aaData = array();
$data = $dbc->GetResult();

$sql = "SELECT SUM(bs_purchase_usd_profit_bwd.amount) AS amount , SUM(bs_purchase_usd_profit_bwd.amount*bs_purchase_usd_profit_bwd.rate_finance) AS total FROM bs_purchase_usd_profit_bwd
    WHERE " . $where;

$rst = $dbc->Query($sql);
$line = $dbc->Fetch($rst);

$data['total'] = array(
    "remian_unmatch" => $line[0],
    "remain_matching" => $total_remain,
    "remain_total" => $line[0] + $total_remain,
    "remain_total_thb" => $line[1] + $total_remain
);


echo json_encode($data);

$dbc->Close();
