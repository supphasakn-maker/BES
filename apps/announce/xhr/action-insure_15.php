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

if ($_POST['insure_15'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input rate insure 15 grams!'
    ));
} else {
    $insure_15 = $os->save_variable("insure_15", $_POST['insure_15']);
    $os->save_log(0, $_SESSION['auth']['user_id'], "change-rate-insure_15", $_POST['insure_15'], array(
        'exchange' => $_POST['insure_15'],
        'date' => date('Y-m-d H:i:s')
    ));
    echo json_encode(array('success' => true));
}

$dbc->Close();
