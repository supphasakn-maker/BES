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

    $VERSION = 'cancel_pack_v1';

    if (empty($_SESSION['auth']['user_id'])) {
        throw new Exception('คุณหมดเวลาการใช้งานแล้ว โปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
    }

    if (empty($_POST['id'])) {
        throw new Exception('Order ID เป็นค่าที่จำเป็น');
    }

    $order_id = intval($_POST['id']);

    $parent_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
    if (!$parent_order) {
        throw new Exception('ไม่พบรายการ Order ที่ระบุ');
    }
    if (!is_null($parent_order['parent'])) {
        throw new Exception('กรุณาเลือก Order หลัก (parent) เพื่อยกเลิกการแพ็ค');
    }

    $dbc->Query("START TRANSACTION");

    $updated_orders_count = 0;

    if ($dbc->Update("bs_orders_bwd", array(
        "#delivery_date"          => "NULL",
        "delivery_pack"           => 0,
        "#delivery_pack_updated"  => "NULL",
        "#accept"                 => 0,
        "#accept_date"            => "NULL"
    ), "id=" . $order_id)) {
        $updated_orders_count++;
    }

    $child_rst = $dbc->Query("SELECT id FROM bs_orders_bwd WHERE parent=" . $order_id);
    while ($child = $dbc->Fetch($child_rst)) {
        $cid = intval($child['id']);
        if ($dbc->Update("bs_orders_bwd", array(
            "#delivery_date"          => "NULL",
            "delivery_pack"           => 0,
            "#delivery_pack_updated"  => "NULL",
            "#accept"                 => 0,
            "#accept_date"            => "NULL"
        ), "id=" . $cid)) {
            $updated_orders_count++;
        }
    }

    $delivery_ids = [];
    $rst_dids = $dbc->Query("
        SELECT DISTINCT delivery_id
        FROM bs_orders_bwd
        WHERE (id = {$order_id} OR parent = {$order_id})
          AND delivery_id IS NOT NULL
    ");
    while ($row = $dbc->Fetch($rst_dids)) {
        $delivery_ids[] = intval($row['delivery_id']);
    }

    $updated_deliveries = 0;
    if (!empty($delivery_ids)) {
        $delivery_ids = array_values(array_unique(array_filter($delivery_ids, fn($v) => $v > 0)));
        if (!empty($delivery_ids)) {
            $in = implode(',', $delivery_ids);
            if ($dbc->Update("bs_deliveries_bwd", array(
                "#delivery_date" => "NULL"
            ), "id IN (" . $in . ")")) {
                $updated_deliveries = count($delivery_ids);
            }
        }
    }

    $dbc->Query("COMMIT");

    $os->save_log(
        0,
        $_SESSION['auth']['user_id'],
        "cancel-pack",
        $order_id,
        array(
            "version" => $VERSION,
            "parent_order_id" => $order_id,
            "updated_orders_count" => $updated_orders_count,
            "updated_delivery_ids" => $delivery_ids
        )
    );

    echo json_encode(array(
        'success'            => true,
        'version'            => $VERSION,
        'file'               => __FILE__,
        'msg'                => 'ยกเลิกการแพ็คเรียบร้อยแล้ว (ออเดอร์ที่อัปเดต: ' . $updated_orders_count . ' รายการ)',
        'parent_order_id'    => $order_id,
        'updated_orders'     => $updated_orders_count,
        'updated_deliveries' => $updated_deliveries
    ));
} catch (Exception $e) {
    if (isset($dbc)) {
        @$dbc->Query("ROLLBACK");
    }
    echo json_encode(array(
        'success' => false,
        'version' => 'cancel_pack_v1',
        'file'    => __FILE__,
        'msg'     => 'Error: ' . $e->getMessage()
    ));
    error_log('Cancel pack error: ' . $e->getMessage() . ' @' . __FILE__);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
