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
    'purchase_type' => $_POST['purchase_type'],
    'amount' => $_POST['amount'],
    'rate_spot' => $_POST['rate_spot'],
    'rate_pmdc' => $_POST['rate_pmdc'],
);

if ($dbc->Update("bs_smg_stx_claim", $data, "id=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $claim = $dbc->GetRecord("bs_smg_stx_claim", "*", "id=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "claim-stx-edit", $_POST['id'], array("bs_smg_stx_claim" => $claim));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}


$dbc->Close();
