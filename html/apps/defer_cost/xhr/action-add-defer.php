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

$oz2kg = 32.1507;

if ($_POST['date'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please select date!'
    ));
} else if (!isset($_POST['purchase'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please select purchase!'
    ));
} else if (!isset($_POST['purchase_defer'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please select Defer Spot!'
    ));
} else {
    $purchase = array();
    $purchase_defer = array();

    $amount_purchase = 0;
    $amount_purchase_defer = 0;

    $total_purchase = 0;
    $total_purchase_defer = 0;

    // เพิ่มตัวแปรสำหรับ supplier_id
    $supplier_id = null;
    $supplier_ids = array();

    foreach ($_POST['purchase'] as $purchase_id) {
        $purchase_item = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $purchase_id);
        array_push($purchase, $purchase_item);

        // เก็บ supplier_id ทั้งหมด
        $supplier_ids[] = $purchase_item['supplier_id'];

        $amount_purchase += $purchase_item['amount'];
        $total_purchase += $purchase_item['amount'] * ($purchase_item['rate_spot'] + $purchase_item['rate_pmdc']) * $oz2kg;
    }

    $unique_supplier_ids = array_unique($supplier_ids);
    if (count($unique_supplier_ids) > 1) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'Purchase items must be from the same supplier!'
        ));
        exit;
    }

    $supplier_id = $unique_supplier_ids[0];

    foreach ($_POST['purchase_defer'] as $purchase_id) {
        $purchase_item = $dbc->GetRecord("bs_incoming_plans", "*", "id=" . $purchase_id);
        array_push($purchase_defer, $purchase_item);

        $amount_purchase_defer += $purchase_item['amount'];

        $total_purchase_defer += $purchase_item['usd'];
    }

    if (number_format($amount_purchase, 4) != number_format($amount_purchase_defer, 4)) {
        $text = "Purchase : " . $amount_purchase . "\n";
        $text .= "Purchase Defer : " . $amount_purchase_defer . "\n";

        echo json_encode(array(
            'success' => false,
            'msg' => $text . "Amount is not match!"
        ));
    } else {
        $value_profit = $total_purchase_defer - $total_purchase;

        $data = array(
            '#id' => "DEFAULT",
            "date_defer" => $_POST['date'],
            '#created' => 'NOW()',
            '#updated' => 'NOW()',
            "#value_defer_spot" => $total_purchase_defer,
            "#value_net" => $total_purchase,
            "#defer" => $value_profit,
            "#user" => $os->auth['id'],
            "#supplier_id" => $supplier_id  // เพิ่ม supplier_id
        );

        if ($dbc->Insert("bs_defer_cost", $data)) {
            $adjust_id = $dbc->GetID();
            echo json_encode(array(
                'success' => true,
                'msg' => $adjust_id
            ));

            foreach ($purchase as $item) {
                $dbc->Update("bs_purchase_spot", array("#defer_id" => $adjust_id), "id=" . $item['id']);
            }

            foreach ($purchase_defer as $item) {
                $dbc->Update("bs_incoming_plans", array("#defer_id" => $adjust_id), "id=" . $item['id']);
            }

            $adjust = $dbc->GetRecord("bs_defer_cost", "*", "id=" . $adjust_id);
            $os->save_log(0, $_SESSION['auth']['user_id'], "bs_defer_cost-add", $adjust_id, array("bs_defer_cost" => $adjust));
        } else {
            echo json_encode(array(
                'success' => false,
                'msg' => "Insert Error"
            ));
        }
    }
}

$dbc->Close();
