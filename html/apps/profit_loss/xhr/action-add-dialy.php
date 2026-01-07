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
        '#id' => "DEFAULT",
        "date" => $_POST['date'],
        '#created' => 'NOW()',
        '#updated' => 'NOW()',
        "#user" => $os->auth['id'],
        "comment" => $_POST['comment']
    );

    if ($dbc->Insert("bs_profit_daily ", $data)) {
        $usd_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $usd_id
        ));
        $usd = $dbc->GetRecord("bs_profit_daily ", "*", "id=" . $usd_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "daily-noted-add", $usd_id, array("usd" => $usd));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
