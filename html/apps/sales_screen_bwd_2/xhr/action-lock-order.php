<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);


header('Content-Type: application/json');

try {
    $dbc = new dbc;
    $dbc->Connect();
    $os = new oceanos($dbc);


    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Order ID is required');
    }

    $order_id = intval($_POST['id']);
    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);

    if (!$order) {
        throw new Exception('Order not found');
    }


    $clear_data = array(
        "#delivery_date" => "NULL",
        "#delivery_id" => "NULL"
    );

    $updated_orders = [];
    $is_parent = is_null($order['parent']);

    if ($is_parent) {



        if ($order['delivery_id']) {
            $dbc->Delete("bs_deliveries_bwd", "id=" . $order['delivery_id']);
        }


        $result = $dbc->Update("bs_orders_bwd", $clear_data, "id=" . $order_id);
        if ($result) {
            $updated_orders[] = $order_id;
        }


        $child_sql = "SELECT id FROM bs_orders_bwd WHERE parent = " . $order_id;
        $child_rst = $dbc->Query($child_sql);

        while ($child = $dbc->Fetch($child_rst)) {
            $child_result = $dbc->Update("bs_orders_bwd", $clear_data, "id=" . $child['id']);
            if ($child_result) {
                $updated_orders[] = $child['id'];
            }
        }
    } else {



        $result = $dbc->Update("bs_orders_bwd", $clear_data, "id=" . $order_id);
        if ($result) {
            $updated_orders[] = $order_id;
        }



        $parent_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order['parent']);
        if ($parent_order && $parent_order['delivery_id']) {

            $remaining_deliveries_sql = "SELECT COUNT(*) as count FROM bs_orders_bwd 
                                        WHERE parent = " . $order['parent'] . " 
                                        AND delivery_date IS NOT NULL 
                                        AND id != " . $order_id;
            $remaining_rst = $dbc->Query($remaining_deliveries_sql);
            $remaining = $dbc->Fetch($remaining_rst);


            $parent_has_delivery = !is_null($parent_order['delivery_date']);


            if ($remaining['count'] == 0 && !$parent_has_delivery) {
                $dbc->Delete("bs_deliveries_bwd", "id=" . $parent_order['delivery_id']);

                $dbc->Update("bs_orders_bwd", array("#delivery_id" => "NULL"), "id=" . $order['parent']);
            }
        }
    }

    if (count($updated_orders) > 0) {

        $updated_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "order-bwd-lock", $order_id, array("bwd-orders" => $updated_order));

        echo json_encode(array(
            'success' => true,
            'msg' => 'Lock order สำเร็จ (' . count($updated_orders) . ' รายการ)',
            'locked_orders' => $updated_orders
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ));
    error_log('Lock order error: ' . $e->getMessage());
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
