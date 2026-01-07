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

if ($dbc->HasRecord("bs_smg_stx_rate_rollover", "date = '" . $_POST['date'] . "' AND id !=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Interest Rollover Name is already exist.'
    ));
} else {
    $data = array(
        'date' => $_POST['date'],
        '#rate_short' => $_POST['rate_short'],
        '#rate' => $_POST['rate'],
        '#interest' => $_POST['interest']
    );

    if ($dbc->Update("bs_smg_stx_rate_rollover", $data, "id=" . $_POST['id'])) {
        echo json_encode(array(
            'success' => true
        ));
        $int_rollover = $dbc->GetRecord("bs_smg_stx_rate_rollover", "*", "id=" . $_POST['id']);
        $os->save_log(0, $_SESSION['auth']['user_id'], "int_rollover-stx-edit", $_POST['id'], array("bs_smg_stx_rate_rollover" => $int_rollover));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
