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


$spot = $dbc->GetRecord("bs_match_usd", "*", "id=" . $_POST['item']);

$dbc->Delete("bs_match_usd", "id=" . $_POST['item']);

$os->save_log(0, $_SESSION['auth']['user_id'], "bs_match_usd-delete", $id, array("bs_match_usd" => $spot));


$dbc->Close();
