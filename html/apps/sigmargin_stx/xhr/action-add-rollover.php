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

if ($_POST['type'] == "Buy") {
    $entry = 'debit';
} else if ($_POST['type'] == "Sell") {
    $entry = 'credit';
}
$data = array(
    'trade' => $_POST['trade'],
    'date' => $_POST['date'],
    'type' => $_POST['type'],
    'amount' => $_POST['amount'],
    'rate_spot' => $_POST['rate_spot'],
    'entry' => $entry,
);

if ($dbc->Insert("bs_smg_stx_rollover", $data)) {
    $rollover_id = $dbc->GetID();
    echo json_encode(array(
        'success' => true,
        'msg' => $rollover_id
    ));

    $rollover = $dbc->GetRecord("bs_smg_stx_rollover", "*", "id=" . $rollover_id);
    $os->save_log(0, $_SESSION['auth']['user_id'], "rollover-stx-add", $rollover_id, array("bs_smg_stx_rollover" => $rollover));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "Insert Error"
    ));
}


$dbc->Close();
