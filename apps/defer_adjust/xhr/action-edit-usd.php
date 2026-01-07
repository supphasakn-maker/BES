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

if ($_POST['usd'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Your Value should not empty!'
    ));
} else {

    $data = array(
        "#bank" => $_POST['bank'],
        "date" => $_POST['date'],
        "#usd	" => $_POST['usd'],
        "comment" => isset($_POST['comment']) ? addslashes($_POST['comment']) : "",
    );

    if ($dbc->Update("bs_match_usd", $data, "id=" . $_POST['id'])) {
        echo json_encode(array(
            'success' => true
        ));
        $spot = $dbc->GetRecord("bs_match_usd", "*", "id=" . $_POST['id']);
        $os->save_log(0, $_SESSION['auth']['user_id'], "usd-edit", $_POST['id'], array("bs_match_usd" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
