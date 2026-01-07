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
    "remark" => $_POST['remark'],
    "usd_debit" => $_POST['usd_debit'],
    "usd_credit	" => $_POST['usd_credit'],
    "amount_debit" => $_POST['amount_debit'],
    "amount_credit" => $_POST['amount_credit'],
);

if ($dbc->Insert("bs_smg_stx_other", $data)) {
    $ohter_id = $dbc->GetID();
    echo json_encode(array(
        'success' => true,
        'msg' => $ohter_id
    ));

    $ohter = $dbc->GetRecord("bs_smg_stx_other", "*", "id=" . $ohter_id);
    $os->save_log(0, $_SESSION['auth']['user_id'], "ohter-stx-add", $ohter_id, array("bs_smg_stx_other" => $ohter));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "Insert Error"
    ));
}

$dbc->Close();
