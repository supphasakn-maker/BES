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


    if (!isset($_POST['items']) || !is_array($_POST['items'])) {
        throw new Exception('ไม่พบข้อมูลรายการที่ต้องการลบ');
    }

    $deleted_count = 0;
    $errors = [];

    foreach ($_POST['items'] as $item) {
        $item = intval($item);

        if ($item <= 0) {
            $errors[] = "รายการ ID ไม่ถูกต้อง: $item";
            continue;
        }

        if (!$dbc->HasRecord("bs_orders_bwd", "id=" . $item)) {
            $errors[] = "ไม่พบรายการ ID: $item";
            continue;
        }

        $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $item);

        if (!$order) {
            $errors[] = "ไม่สามารถดึงข้อมูลรายการ ID: $item";
            continue;
        }

        if (!is_null($order['parent'])) {

            $deletable = true;
            $aDelete = array();
            $sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $order['parent'] . " AND id != " . $item;
            $rst = $dbc->Query($sql);

            while ($line = $dbc->Fetch($rst)) {
                if ($dbc->HasRecord("bs_orders_bwd", "parent=" . $line['id'])) {
                    $deletable = false;
                }
                array_push($aDelete, $line['id']);
            }

            if ($deletable) {

                foreach ($aDelete as $id) {
                    $result = $dbc->Delete("bs_orders_bwd", "id=" . $id);
                    if (!$result) {
                        $errors[] = "ไม่สามารถลบออเดอร์ลูก ID: $id";
                    }
                }


                $result = $dbc->Delete("bs_orders_bwd", "id=" . $item);
                if (!$result) {
                    $errors[] = "ไม่สามารถลบออเดอร์ ID: $item";
                    continue;
                }


                $parent_order = $dbc->GetRecord("bs_orders_bwd", "delivery_id", "id=" . $order['parent']);


                if ($parent_order && $parent_order['delivery_id']) {
                    $dbc->Delete("bs_deliveries_bwd", "id=" . $parent_order['delivery_id']);
                }


                $dbc->Update("bs_orders_bwd", array('#status' => 1, '#updated' => 'NOW()'), "id=" . $order['parent']);

                $deleted_count++;
            } else {
                $errors[] = "ไม่สามารถลบออเดอร์ ID: $item เนื่องจากมีรายการย่อย";
            }
        } else {



            $delivery_id = $order['delivery_id'];


            $child_orders_sql = "SELECT id FROM bs_orders_bwd WHERE parent = " . $item;
            $child_rst = $dbc->Query($child_orders_sql);

            while ($child = $dbc->Fetch($child_rst)) {
                $result = $dbc->Delete("bs_orders_bwd", "id=" . $child['id']);
                if (!$result) {
                    $errors[] = "ไม่สามารถลบออเดอร์ลูก ID: " . $child['id'];
                }
            }


            $result = $dbc->Delete("bs_orders_bwd", "id=" . $item);
            if (!$result) {
                $errors[] = "ไม่สามารถลบออเดอร์หลัก ID: $item";
                continue;
            }


            if ($delivery_id) {
                $dbc->Delete("bs_deliveries_bwd", "id=" . $delivery_id);
            }


            try {
                $os->save_log(0, $_SESSION['auth']['user_id'], "order-bwd-delete", $item, array("bs_orders_bwd" => $order));
            } catch (Exception $log_error) {

                error_log("Log save error: " . $log_error->getMessage());
            }

            $deleted_count++;
        }
    }


    $response = [
        'success' => true,
        'deleted_count' => $deleted_count,
        'message' => "ลบรายการสำเร็จ $deleted_count รายการ"
    ];

    if (!empty($errors)) {
        $response['warnings'] = $errors;
        $response['message'] .= " (มีข้อผิดพลาดบางรายการ)";
    }

    echo json_encode($response);
} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
