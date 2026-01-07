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


$reserve = $dbc->GetRecord("bs_smg_rollover_type", "*", "id=" . $_POST['type']);

echo json_encode(array(
    'reserve' => $reserve
));


$dbc->Close();
