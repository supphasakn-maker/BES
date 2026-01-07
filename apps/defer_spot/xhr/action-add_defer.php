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
} else if ($_POST['price'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Your value should not empty!'
    ));
} else {
    $data = array(
        '#id' => "DEFAULT",
        "#supplier_id" => $_POST['supplier_id'],
        "#amount" => $_POST['amount'],
        "#rate_spot" => $_POST['rate_spot'],
        "#rate_pmdc" => $_POST['rate_pmdc'],
        "#price" => $_POST['price'],
        "value_date" => $_POST['value_date'],
        '#created' => 'NOW()',
        '#updated' => 'NOW()',
        "ref" => $_POST['ref'],
        "#user" => $os->auth['id'],
        "#status" => 1,
        "#defer_id" => 'NULL'
    );

    if ($dbc->Insert("bs_defer_spot", $data)) {
        $spot_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $spot_id
        ));
        $spot = $dbc->GetRecord("bs_defer_spot", "*", "id=" . $spot_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "defer-add", $spot_id, array("bs_defer_spot" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
