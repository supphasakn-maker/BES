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
    'usd_debit' => $_POST['usd_debit'],
    'usd_credit' => $_POST['usd_credit'],
    'amount_debit' => $_POST['amount_debit'],
    'amount_credit' => $_POST['amount_credit'],
    'date' => $_POST['date'],
    'remark' => $_POST['remark'],
);

if ($dbc->Update("bs_smg_stx_other", $data, "id=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $ohter = $dbc->GetRecord("bs_smg_stx_other", "*", "id=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "ohter-stx-edit", $_POST['id'], array("bs_smg_stx_other" => $ohter));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}

$dbc->Close();
