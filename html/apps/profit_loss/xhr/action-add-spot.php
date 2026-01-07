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
} else if ($_POST['amount'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Your Amount should not empty!'
    ));
} else {
    $data = array(
        '#id' => "DEFAULT",
        "#supplier_id" => $_POST['supplier_id'],
        "type" => $_POST['type'],
        "noted" => $_POST['noted'],
        "#amount" => $_POST['amount'],
        "#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : "NULL",
        "#rate_pmdc" => $_POST['rate_pmdc'] != "" ? $_POST['rate_pmdc'] : "NULL",
        "#THBValue" => $_POST['THBValue'] != "" ? $_POST['THBValue'] : "NULL",
        "currency" => $_POST['currency'],
        "date" => $_POST['date'],
        "value_date" => $_POST['value_date'],
        '#created' => 'NOW()',
        '#updated' => 'NOW()',
        "method" => $_POST['method'],
        "ref" => $_POST['ref'],
        "#user" => $os->auth['id'],
        "#status" => isset($_POST['pending']) ? 0 : 1,
        "#confirm" => isset($_POST['pending']) ? 'NULL' : 'NOW()',
        "#order_id" => 'NULL',
        "comment" => isset($_POST['comment']) ? $_POST['comment'] : "",
        "#adj_supplier" => $_POST['adj_supplier'],
        "#product_id" => $_POST['product_id']
    );


    if ($dbc->Insert("bs_purchase_spot_profit", $data)) {
        $spot_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $spot_id
        ));



        $spot = $dbc->GetRecord("bs_purchase_spot_profit", "*", "id=" . $spot_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "spot-profit-add", $spot_id, array("spot" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
