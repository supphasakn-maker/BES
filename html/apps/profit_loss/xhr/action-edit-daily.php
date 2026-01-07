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
} else {
    $data = array(
        "date" => $_POST['date'],
        "comment" => $_POST['comment'],
        '#updated' => 'NOW()'
    );

    if ($dbc->Update("bs_profit_daily", $data, "id=" . $_POST['id'])) {
        echo json_encode(array(
            'success' => true
        ));
        $usd = $dbc->GetRecord("bs_profit_daily", "*", "id=" . $_POST['id']);
        $os->save_log(0, $_SESSION['auth']['user_id'], "daily-noted-edit", $_POST['id'], array("usd" => $usd));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
