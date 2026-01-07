<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();


if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => "No ID provided"
    ));
    exit;
}

$order_id = intval($_POST['id']);
$remove_reason = isset($_POST['remove_reason']) ? $_POST['remove_reason'] : 'No reason provided';

try {

    $dbc->query("START TRANSACTION");


    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);

    if (!$order) {
        throw new Exception("Order not found");
    }

    $updated_count = 0;


    $is_main_order = is_null($order['parent']);

    if ($is_main_order) {

        if (isset($order['delivery_id']) && !empty($order['delivery_id'])) {
            $delivery_id = intval($order['delivery_id']);
            $dbc->Delete("bs_deliveries_bwd", "id=" . $delivery_id);
        }


        $order_data = array(
            'remove_reason' => $remove_reason,
            '#updated' => 'NOW()',
            '#status' => -1,
            '#delivery_id' => 'NULL'
        );

        if ($dbc->Update("bs_orders_bwd", $order_data, "id=" . $order_id)) {
            $updated_count++;
        }


        $sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $order_id . " AND status > 0";
        $result = $dbc->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($sub_order = $result->fetch_assoc()) {
                $sub_order_data = array(
                    'remove_reason' => $remove_reason,
                    '#updated' => 'NOW()',
                    '#status' => -1
                );

                if ($dbc->Update("bs_orders_bwd", $sub_order_data, "id=" . $sub_order['id'])) {
                    $updated_count++;
                }
            }
        }
    } else {

        $order_data = array(
            'remove_reason' => $remove_reason,
            '#updated' => 'NOW()',
            '#status' => -1
        );

        if ($dbc->Update("bs_orders_bwd", $order_data, "id=" . $order_id)) {
            $updated_count++;
        }
    }

    if ($updated_count > 0) {

        $dbc->query("COMMIT");

        $message = "Deleted " . $updated_count . " order(s) successfully";
        if ($is_main_order && isset($order['delivery_id']) && !empty($order['delivery_id'])) {
            $message .= " (delivery removed)";
        }

        echo json_encode(array(
            'success' => true,
            'msg' => $message
        ));
    } else {
        throw new Exception("No orders were updated");
    }
} catch (Exception $e) {

    $dbc->query("ROLLBACK");

    echo json_encode(array(
        'success' => false,
        'msg' => $e->getMessage()
    ));
}

$dbc->Close();
