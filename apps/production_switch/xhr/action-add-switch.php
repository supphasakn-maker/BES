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



if ($dbc->HasRecord("bs_productions_switch", "round = '" . $_POST['import_lot'] . "' AND status != 2")) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Round is already exist.'
    ));
} else if ($_POST['balance'] < 0) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'จำนวนต้องไม่ติดลบ'
    ));
} else {
    $data = array(
        '#id' => "DEFAULT",
        'round' => $_POST['import_lot'],
        'round_turn' => $_POST['round_turn_id'],
        '#created' => 'NOW()',
        '#updated' => 'NOW()',
        '#user' => $os->auth['id'],
        'remark' => $_POST['remark'],
        "#weight_out_packing" => $_POST['amount'],
        "#weight_out_total" => $_POST['balance'],
        "#product_id" => $_POST['product_type_id'],
        "#product_id_turn" => $_POST['product_id_turn'],
        '#status' => 0,

    );



    if ($dbc->Insert("bs_productions_switch", $data)) {
        $prepare_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $prepare_id
        ));

        $prepare = $dbc->GetRecord("bs_productions_switch", "*", "id=" . $prepare_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "switch-add", $prepare_id, array("switch" => $prepare));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
