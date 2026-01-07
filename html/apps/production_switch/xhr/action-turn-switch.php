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
    '#status' => 2,
    '#mapping' => 'NOW()'
);

if ($dbc->Update("bs_switch_pack_items", $data, "switch_id	=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $spot = $dbc->GetRecord("bs_switch_pack_items", "*", "switch_id	=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "turn-product", $_POST['id'], array("turn-product" => $spot));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}


$dbc->Close();
