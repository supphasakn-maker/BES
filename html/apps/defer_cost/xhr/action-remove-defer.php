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

$adjust = $dbc->GetRecord("bs_defer_cost", "*", "id=" . $_POST['id']);

$dbc->Delete("bs_defer_cost", "id=" . $_POST['id']);
$dbc->Update("bs_purchase_spot", array("#defer_id" => "NULL"), "defer_id=" . $_POST['id']);
$dbc->Update("bs_incoming_plans", array("#defer_id" => "NULL"), "defer_id=" . $_POST['id']);

$os->save_log(0, $_SESSION['auth']['user_id'], "defer-delete", $id, array("defer" => $adjust));



$dbc->Close();
