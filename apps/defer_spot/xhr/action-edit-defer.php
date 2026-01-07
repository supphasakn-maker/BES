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

if ($_POST['price'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Your Value should not empty!'
    ));
} else {

    $data = array(
        "#supplier_id" => $_POST['supplier_id'],
        "#amount" => $_POST['amount'],
        "#rate_spot" => $_POST['rate_spot'],
        "#rate_pmdc" => $_POST['rate_pmdc'],
        "#price" => $_POST['price'],
        "value_date" => $_POST['value_date'],
        '#created' => 'NOW()',
        '#updated' => 'NOW()',
        "ref" => $_POST['ref'],
        "#user" => $os->auth['id']
    );

    if ($dbc->Update("bs_defer_spot", $data, "id=" . $_POST['id'])) {
        echo json_encode(array(
            'success' => true
        ));
        $spot = $dbc->GetRecord("bs_defer_spot", "*", "id=" . $_POST['id']);
        $os->save_log(0, $_SESSION['auth']['user_id'], "defer-edit", $_POST['id'], array("bs_defer_spot" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
