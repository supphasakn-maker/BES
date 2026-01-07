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


    if (empty($_SESSION['auth']['user_id'])) {
        throw new Exception('คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
    }


    if (!isset($_POST['id']) || !isset($_POST['delivery_date']) || empty($_POST['delivery_date'])) {
        throw new Exception('Order ID และ Delivery Date เป็นค่าที่จำเป็น');
    }

    $order_id = intval($_POST['id']);
    $delivery_date = $_POST['delivery_date'];


    $parent_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
    if (!$parent_order) {
        throw new Exception('ไม่พบรายการ Order ที่ระบุ');
    }


    if (!is_null($parent_order['parent'])) {
        throw new Exception('กรุณาเลือก Order หลัก (parent) เพื่อสร้างรายการส่งสินค้า');
    }


    $data = array(
        "#id" => "DEFAULT",
        "#type" => 1,
        "delivery_date" => $delivery_date,
        "#created" => "NOW()",
        "#updated" => "NOW()",
        "#status" => 0,
        "#amount" => $parent_order['amount'],
        "#user" => $os->auth['id'],
        "comment" => ""
    );


    if (!$dbc->Insert("bs_deliveries_bwd", $data)) {
        throw new Exception("ไม่สามารถสร้าง Delivery ได้");
    }

    $delivery_id = $dbc->GetID();
    $code = "DB-" . sprintf("%07s", $delivery_id);


    $dbc->Update("bs_deliveries_bwd", array("code" => $code), "id=" . $delivery_id);

    $updated_orders_count = 0;


    if ($dbc->Update("bs_orders_bwd", array(
        "delivery_id" => $delivery_id,
        "delivery_date" => $delivery_date
    ), "id=" . $order_id)) {
        $updated_orders_count++;
    }


    $child_sql = "SELECT id FROM bs_orders_bwd WHERE parent = " . $order_id;
    $child_rst = $dbc->Query($child_sql);

    while ($child = $dbc->Fetch($child_rst)) {
        if ($dbc->Update("bs_orders_bwd", array(
            "delivery_id" => $delivery_id,
            "delivery_date" => $delivery_date
        ), "id=" . $child['id'])) {
            $updated_orders_count++;
        }
    }


    $os->save_log(0, $_SESSION['auth']['user_id'], "create-delivery-with-children", $delivery_id, array("parent_order_id" => $order_id, "code" => $code));


    echo json_encode(array(
        'success' => true,
        'msg' => 'สร้างรายการส่งสินค้าเรียบร้อยแล้ว (' . $updated_orders_count . ' รายการ)',
        'code' => $code
    ));
} catch (Exception $e) {

    echo json_encode(array(
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ));
    error_log('Create delivery error: ' . $e->getMessage());
} finally {

    if (isset($dbc)) {
        $dbc->Close();
    }
}
