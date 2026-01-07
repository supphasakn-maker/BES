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

$split = $dbc->GetRecord("bs_spot_usd_splited", "*", "id=" . $_POST['id']);

$dbc->Delete("bs_spot_usd_splited", "purchase_id=" . $split['purchase_id']);
$dbc->Update("bs_purchase_spot", array("#status" => 1), "id=" . $split['purchase_id']);
$dbc->Update("bs_purchase_spot_profit", array("#status" => 1), "id=" . $split['purchase_id']);
$os->save_log(0, $_SESSION['auth']['user_id'], "defer-split-delete", $import['import_id'], array("bs_spot_usd_splited" => $split));





$dbc->Close();
