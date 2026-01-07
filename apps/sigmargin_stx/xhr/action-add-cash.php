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

if ($dbc->Insert("bs_smg_stx_cash", $data)) {
    $cash_id = $dbc->GetID();
    echo json_encode(array(
        'success' => true,
        'msg' => $cash_id
    ));

    $cash = $dbc->GetRecord("bs_smg_stx_cash", "*", "id=" . $cash_id);
    $os->save_log(0, $_SESSION['auth']['user_id'], "cash-stx-add", $cash_id, array("bs_smg_stx_cash" => $cash));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "Insert Error"
    ));
}


$dbc->Close();
