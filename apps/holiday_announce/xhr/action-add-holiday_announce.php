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

$PublicHoliday = $_POST['PublicHoliday'];
$FisYear = date('Y', strtotime($PublicHoliday));

$data = array(
    '#id' => "DEFAULT",
    'FisYear' => $FisYear,
    "PublicHoliday" => addslashes($PublicHoliday),
    "Descripiton" => addslashes($_POST['Descripiton'])
);

if ($dbc->Insert("a_public_holiday", $data)) {
    $customer_id = $dbc->GetID();
    echo json_encode(array(
        'success' => true,
        'msg' => $customer_id
    ));

    $customer = $dbc->GetRecord("a_public_holiday", "*", "id=" . $customer_id);
    $os->save_log(0, $_SESSION['auth']['user_id'], "holiday-add", $customer_id, array("a_public_holiday" => $customer));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "Insert Error"
    ));
}

$dbc->Close();
