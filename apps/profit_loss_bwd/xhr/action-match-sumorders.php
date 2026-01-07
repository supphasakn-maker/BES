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

$total_order = 0;
$total_total = 0;

for ($i = 0; $i < count($_POST['order_id']); $i++) {
    $total_order += $_POST['order_amount'][$i];
    $total_total += $_POST['order_total'][$i];
}

if (count($_POST['order_id']) < 1) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'There is no order item.'
    ));
} else if ($_POST['date'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input date.'
    ));
} else {

    $data = array(
        "#id" => 'DEFAULT',
        "mapped" => $_POST['date'] . " " . date("H:i:s"),
        "#amount" => $total_order,
        "#total" => $total_total,
        "remark" => $_POST['remark']
    );

    if ($dbc->Insert("bs_mapping_profit_bwd", $data)) {
        $mapping_id = $dbc->GetID();

        for ($i = 0; $i < count($_POST['order_id']); $i++) {
            $current_order_id = $_POST['order_id'][$i];

            // เช็คว่าเป็น Split Order หรือไม่
            $is_split = false;
            $order_calculated_amount = 0;
            $order_total_sum = 0;

            if (is_string($current_order_id) && strpos($current_order_id, 'SPLIT_') === 0) {
                // เป็น Split Order
                $is_split = true;
                $split_id = intval(str_replace('SPLIT_', '', $current_order_id));

                // ดึงข้อมูลจาก bs_orders_split_bwd
                $sql_split = "SELECT parent_order_id, split_amount, split_total 
                              FROM bs_orders_split_bwd 
                              WHERE id = $split_id AND status = 1";
                $rst_split = $dbc->Query($sql_split);
                $split_data = mysqli_fetch_array($rst_split);

                if ($split_data) {
                    $order_calculated_amount = floatval($split_data['split_amount']);
                    $order_total_sum = floatval($split_data['split_total']);
                } else {
                    continue; // ข้ามถ้าไม่เจอข้อมูล split
                }
            } else {
                // เป็น Order ปกติ
                $order_id = intval($current_order_id);
                $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);

                if (!$order) {
                    continue; // ข้ามถ้าไม่เจอ order
                }

                $sql_calc = "SELECT 
                    SUM(CASE 
                        WHEN product_id = 1 THEN amount * 0.015
                        WHEN product_id = 2 THEN amount * 0.050
                        WHEN product_id = 3 THEN amount * 0.150
                        ELSE 0
                    END) AS calculated_amount,
                    SUM(total) AS total_sum
                FROM bs_orders_bwd 
                WHERE (id = " . $order['id'] . " OR parent = " . $order['id'] . ")
                AND status > 0
                AND product_id IN (1,2,3)";

                $rst_calc = $dbc->Query($sql_calc);
                $row_calc = mysqli_fetch_array($rst_calc);
                $order_calculated_amount = floatval($row_calc['calculated_amount']);
                $order_total_sum = floatval($row_calc['total_sum']);
            }

            $data = array(
                "#id" => 'DEFAULT',
                "#mapping_id" => $mapping_id,
                "order_id" => $current_order_id, // เก็บเป็น string ถ้าเป็น SPLIT_xxx
                "#amount" => $_POST['order_amount'][$i],
                "#total" => $_POST['order_total'][$i],
            );

            if ($_POST['order_mapping_id'][$i] == "") {
                $dbc->Insert("bs_mapping_profit_orders_bwd", $data);

                if ($order_calculated_amount > $_POST['order_amount'][$i]) {
                    $data["#amount"] = $order_calculated_amount - $_POST['order_amount'][$i];
                    $data["#total"] = $order_total_sum - $_POST['order_total'][$i];
                    $data["#mapping_id"] = "NULL";

                    $dbc->Insert("bs_mapping_profit_orders_bwd", $data);
                }
            } else {
                $mapping = $dbc->GetRecord("bs_mapping_profit_orders_bwd", "*", "id=" . $_POST['order_mapping_id'][$i]);

                if ($mapping['amount'] > $_POST['order_amount'][$i]) {
                    $data["#amount"] = $mapping['amount'] - $_POST['order_amount'][$i];
                    $data["#total"] = $mapping['total'] - $_POST['order_total'][$i];
                    $data["#mapping_id"] = "NULL";
                    $dbc->Insert("bs_mapping_profit_orders_bwd", $data);
                }

                $dbc->Update("bs_mapping_profit_orders_bwd", array(
                    "#amount" => $_POST['order_amount'][$i],
                    "#total" => $_POST['order_total'][$i],
                    "#mapping_id" => $mapping_id
                ), "id=" . $_POST['order_mapping_id'][$i]);
            }
        }

        echo json_encode(array(
            'success' => true,
            'msg' => $mapping_id
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
