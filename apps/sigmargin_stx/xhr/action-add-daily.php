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



if ($dbc->HasRecord("bs_smg_stx_daily", "date = '" . $_POST['date'] . "'")) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Daily Name is already exist.'
    ));
} else {
    $data = array(
        '#id' => "DEFAULT",
        "date" => $_POST['date'],
        "#rollover" => $_POST['rollover'],
        "#spot_sell" => $_POST['spot_sell'],
        "#spot_buy" => $_POST['spot_buy'],
        "#cash" => $_POST['cash'],
    );

    if ($dbc->Insert("bs_smg_stx_daily", $data)) {
        $daily_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $daily_id
        ));

        $daily = $dbc->GetRecord("bs_smg_stx_daily", "*", "id=" . $daily_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "bs_smg_stx_daily-add", $daily_id, array("dailys" => $daily));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
