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
    '#interest_match' => $_POST['interest_match'] != "" ? $_POST['interest_match'] : "0.0000",
    '#updated' => 'NOW()'
);

if ($dbc->Update("bs_transfers", $data, "id=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $contract = $dbc->GetRecord("bs_transfers", "*", "id=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "contract-edit_Interest", $_POST['id'], array("contracts" => $contract));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}


$dbc->Close();
