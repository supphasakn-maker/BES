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
    'date' => $_POST['date'],
    'amount' => $_POST['amount'],
    'rate_pmdc' => $_POST['rate_pmdc'],
    'transfer' => $_POST['transfer']
);

if ($dbc->Insert("bs_smg_stx_receiving", $data)) {
    $incoming_id = $dbc->GetID();
    echo json_encode(array(
        'success' => true,
        'msg' => $incoming_id
    ));

    $incoming = $dbc->GetRecord("bs_smg_stx_receiving", "*", "id=" . $incoming_id);
    $os->save_log(0, $_SESSION['auth']['user_id'], "incoming-stx-add", $incoming_id, array("bs_smg_stx_receiving" => $incoming));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "Insert Error"
    ));
}


$dbc->Close();
