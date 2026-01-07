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


$data = array(
    'date' => $_POST['date'],
    'type' => $_POST['type'],
    'amount_usd' => $_POST['amount_usd'],
    'rate_pmdc' => $_POST['rate_pmdc'],
    'amount_total' => $_POST['amount_total'],
);

if ($dbc->Update("bs_smg_stx_payment", $data, "id=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $transfer = $dbc->GetRecord("bs_smg_stx_payment", "*", "id=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "transfer-stx-edit", $_POST['id'], array("transfers" => $transfer));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}


$dbc->Close();
