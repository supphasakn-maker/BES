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



$data = array(
    '#id' => "DEFAULT",
    "#type_id" => $_POST['type_id'],
    "#product_id" => $_POST['product_id'],
    "remark" => $_POST['remark'],
    "code_no" => $_POST['code_no'],
    "#amount" => $_POST['amount'],
    '#created' => 'NOW()',
    '#updated' => 'NOW()',
    "date" => $_POST['date']
);

if ($dbc->Insert("bs_stock_adjusted_over", $data)) {
    $adjust_id = $dbc->GetID();
    echo json_encode(array(
        'success' => true,
        'msg' => $adjust_id
    ));

    $adjust = $dbc->GetRecord("bs_stock_adjusted_over", "*", "id=" . $adjust_id);
    $os->save_log(0, $_SESSION['auth']['user_id'], "adjust-over-add", $adjust_id, array("adjusts" => $adjust));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "Insert Error"
    ));
}


$dbc->Close();
