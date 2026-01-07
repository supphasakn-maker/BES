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

if ($_POST['pmdc_grains'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input rate pmdc grains!'
    ));
} else {
    $pmdc_grains = $os->save_variable("pmdc_grains", $_POST['pmdc_grains']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "change-rate-pmdc_grains", $_POST['pmdc_grains'], array(
        'exchange' => $_POST['pmdc_grains'],
        'date' => date('Y-m-d H:i:s')
    ));
    echo json_encode(array('success' => true));
}

$dbc->Close();
