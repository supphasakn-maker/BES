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

if ($_POST['time'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input Time!'
    ));
} else {
    $data = array(
        '#id' => "DEFAULT",
        '#created' => 'NOW()',
        '#updated' => 'NOW()',
        'round' => $_POST['round'],
        'bar' => $_POST['bar'],
        'amount' => $_POST['amount'],
        'date' => $_POST['date'],
        'time' => $_POST['time'],
        'user' => $_POST['user'],
        '#status' => 0
    );


    if ($dbc->Insert("bs_productions_silver_save", $data)) {
        $silver_save_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $silver_save_id
        ));

        $silver_save = $dbc->GetRecord("bs_productions_silver_save", "*", "id=" . $silver_save_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "bs_productions_silver_save-add", $silver_save_id, array("bs_productions_silver_save" => $silver_save));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}
