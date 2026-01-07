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

if (empty($_SESSION['auth']['user_id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
    ));
    exit();
} else if ($_POST['usd'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Your USD should not empty!'
    ));
} else {
    $data = array(
        '#id' => "DEFAULT",
        "#bank" => $_POST['bank'],
        "#usd" => $_POST['usd'],
        "date" => $_POST['date'],
        "comment" => isset($_POST['comment']) ? addslashes($_POST['comment']) : "",
    );

    if ($dbc->Insert("bs_match_usd", $data)) {
        $spot_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $spot_id
        ));


        $spot = $dbc->GetRecord("bs_match_usd", "*", "id=" . $spot_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "usd-add", $spot_id, array("bs_match_usd" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
