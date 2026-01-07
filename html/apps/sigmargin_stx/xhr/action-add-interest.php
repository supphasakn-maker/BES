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

$date_obj = DateTime::createFromFormat('Y-m-d', $_POST['date_start']);
$date_obj->modify('first day of next month');
$date = $date_obj->format('Y-m-d');

$data = array(
    'date_start' => $_POST['date_start'],
    'date_end' => $_POST['date_end'],
    'interest' => $_POST['interest'],
    'date' => $date
);

if ($dbc->Insert("bs_smg_stx_interest", $data)) {
    $interest_id = $dbc->GetID();
    echo json_encode(array(
        'success' => true,
        'msg' => $interest_id
    ));

    $interest = $dbc->GetRecord("bs_smg_stx_interest", "*", "id=" . $interest_id);
    $os->save_log(0, $_SESSION['auth']['user_id'], "interest-stx-add", $interest_id, array("bs_smg_stx_interest" => $interest));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "Insert Error"
    ));
}

$dbc->Close();
