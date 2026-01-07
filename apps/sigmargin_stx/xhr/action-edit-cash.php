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
    "date" => $_POST['date'],
    "amount" => $_POST['amount']
);

if ($dbc->Update("bs_smg_stx_cash", $data, "id=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $cash = $dbc->GetRecord("bs_smg_stx_cash", "*", "id=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "cash-stx-edit", $_POST['id'], array("bs_smg_stx_cash" => $cash));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}

$dbc->Close();
