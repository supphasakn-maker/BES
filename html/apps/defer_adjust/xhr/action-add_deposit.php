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
        "#supplier_id" => $_POST['supplier_id'],
        "#usd" => $_POST['usd'],
        "date" => $_POST['date']
    );
    $datasmg = array(
        '#id' => "DEFAULT",
        "#supplier_id" => $_POST['supplier_id'],
        "#usd" => $_POST['usd'],
        "date" => $_POST['date']
    );
    if ($dbc->Insert("bs_match_deposit", $data)) {
        $spot_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $spot_id
        ));


        $dbc->Insert("bs_match_fx", $datasmg);
        $spot = $dbc->GetRecord("bs_match_deposit", "*", "id=" . $spot_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "deposit-add", $spot_id, array("bs_match_deposit" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
