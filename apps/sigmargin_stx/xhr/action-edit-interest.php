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
    'date_start' => $_POST['date_start'],
    'date_end' => $_POST['date_end'],
    'interest' => $_POST['interest'],
    'date' => $_POST['date']
);

if ($dbc->Update("bs_smg_stx_interest", $data, "id=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $interest = $dbc->GetRecord("bs_smg_stx_interest", "*", "id=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "interest-stx-edit", $_POST['id'], array("bs_smg_stx_interest" => $interest));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}

$dbc->Close();
