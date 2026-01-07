<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_purchase_usd_profit.id",
    "bank" => "bs_purchase_usd_profit.bank",
    "type" => "bs_purchase_usd_profit.type",
    "amount" => "FORMAT(bs_purchase_usd_profit.amount,2)",
    "rate_exchange" => "bs_purchase_usd_profit.rate_exchange",
    "rate_finance" => "bs_purchase_usd_profit.rate_finance",
    "date" => "bs_purchase_usd_profit.date",
    "comment" => "bs_purchase_usd_profit.comment",
    "method" => "bs_purchase_usd_profit.method",
    "ref" => "bs_purchase_usd_profit.ref",
    "user" => "bs_purchase_usd_profit.user",
    "status" => "bs_purchase_usd_profit.status",
    "confirm" => "bs_purchase_usd_profit.confirm",
    "created" => "bs_purchase_usd_profit.created",
    "updated" => "bs_purchase_usd_profit.updated",
    "parent" => "bs_purchase_usd_profit.parent",
    "bank_date" => "bs_purchase_usd_profit.bank_date",
    "premium_start" => "bs_purchase_usd_profit.premium_start",
    "premium" => "bs_purchase_usd_profit.premium",
    "transfer_id" => "bs_purchase_usd_profit.transfer_id",
    "fw_contract_no" => "bs_purchase_usd_profit.fw_contract_no",
    "unpaid" => "bs_purchase_usd_profit.unpaid",
    "value" => "FORMAT(bs_purchase_usd_profit.amount*bs_purchase_usd_profit.rate_finance,2)"
);


$where = " bs_purchase_usd_profit.status <> -1 AND (bs_purchase_usd_profit.type LIKE 'physical' OR bs_purchase_usd_profit.type LIKE 'MTM')
AND YEAR(date) > 2024";


if (isset($_GET['date_filter'])) {
    $where .= " AND (bs_purchase_usd_profit.value_date = '" .  $_GET['date_filter'] . "')";
}

$table = array(
    "index" => "id",
    "name" => "bs_purchase_usd_profit",
    "where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$total_remain = 0;

$aaData = array();
$data = $dbc->GetResult();

$sql = "SELECT SUM(bs_purchase_usd_profit.amount) AS amount , SUM(bs_purchase_usd_profit.amount*bs_purchase_usd_profit.rate_finance) AS total FROM bs_purchase_usd_profit
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
