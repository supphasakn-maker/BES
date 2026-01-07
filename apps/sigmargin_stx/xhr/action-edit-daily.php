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

if ($dbc->HasRecord("bs_smg_stx_daily", "date = '" . $_POST['date'] . "' AND id !=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Daily Name is already exist.'
    ));
} else {
    $data = array(
        "date" => $_POST['date'],
        "#rollover" => $_POST['rollover'],
        "#spot_sell" => $_POST['spot_sell'],
        "#spot_buy" => $_POST['spot_buy'],
        "#cash" => $_POST['cash'],
    );

    if ($dbc->Update("bs_smg_stx_daily", $data, "id=" . $_POST['id'])) {
        echo json_encode(array(
            'success' => true
        ));
        $daily = $dbc->GetRecord("bs_smg_stx_daily", "*", "id=" . $_POST['id']);
        $os->save_log(0, $_SESSION['auth']['user_id'], "bs_smg_stx_daily-edit", $_POST['id'], array("dailys" => $daily));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
