<?php
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed");
    }

    if (!isset($_POST['action'])) {
        throw new Exception("Action is required");
    }

    switch ($_POST['action']) {
        case 'update_coupons':
            updateCoupons($dbc, $_POST['coupons']);
            break;
        case 'get_coupons':
            getCoupons($dbc);
            break;
        case 'get_coupon_stats':
            getCouponStats($dbc);
            break;
        case 'find_coupon':
            findCouponByOrderId($dbc, $_POST['order_id'] ?? '');
            break;
        case 'unused_coupon':
            unusedCoupon($dbc, intval($_POST['id'] ?? 0));
            break;
        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

function updateCoupons($dbc, $coupons_json)
{
    if (empty($coupons_json)) {
        throw new Exception("ไม่มีข้อมูลสำหรับอัพเดท");
    }

    $coupons = json_decode($coupons_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("ข้อมูล JSON ไม่ถูกต้อง");
    }
    if (empty($coupons)) {
        throw new Exception("ไม่มีข้อมูล coupon สำหรับอัพเดท");
    }

    $order_ids = array_column($coupons, 'order_id');
    if (count($order_ids) !== count(array_unique($order_ids))) {
        throw new Exception("มี Order ID ซ้ำกันในรายการที่ส่งมา");
    }

    // // ตรวจซ้ำใน DB (ใช้ fetch loop แทน num_rows)
    // if (!empty($order_ids)) {
    //     $safe_ids = array_map(function ($id) {
    //         return "'" . addslashes(trim($id)) . "'";
    //     }, $order_ids);
    //     $id_list = implode(",", $safe_ids);

    //     $check_sql = "SELECT order_id FROM bs_coupons 
    //                   WHERE order_id IN ($id_list) 
    //                   AND order_id IS NOT NULL 
    //                   AND order_id != ''";
    //     $check_rst = $dbc->Query($check_sql);

    //     $dupes = [];
    //     while ($row = $dbc->Fetch($check_rst)) {
    //         $dupes[] = $row['order_id'];
    //     }

    //     if (!empty($dupes)) {
    //         throw new Exception("Order ID เหล่านี้ถูกใช้แล้ว: " . implode(", ", $dupes));
    //     }
    // }

    $dbc->Query("START TRANSACTION");
    $updated = 0;

    foreach ($coupons as $c) {
        $cid = intval($c['id']);
        $oid = addslashes(trim($c['order_id']));

        $rst = $dbc->Query("SELECT id, status FROM bs_coupons WHERE id=$cid");
        $row = $dbc->Fetch($rst);
        if (!$row) {
            $dbc->Query("ROLLBACK");
            throw new Exception("Coupon ID $cid ไม่พบในระบบ");
        }
        if ($row['status'] == 0) {
            $dbc->Query("ROLLBACK");
            throw new Exception("Coupon ID $cid ถูกใช้งานแล้ว");
        }

        $sql = "UPDATE bs_coupons 
                SET order_id='$oid', status=0, updated_at=NOW() 
                WHERE id=$cid AND status=1";
        $ok = $dbc->Query($sql);
        if ($ok) {
            $updated++;
        } else {
            $dbc->Query("ROLLBACK");
            throw new Exception("อัพเดท Coupon ID $cid ไม่สำเร็จ");
        }
    }

    $dbc->Query("COMMIT");
    echo json_encode([
        "success" => true,
        "message" => "อัพเดทข้อมูล $updated coupon(s) สำเร็จ!",
        "updated_count" => $updated
    ]);
}

function getCoupons($dbc)
{
    $rst = $dbc->Query("SELECT id, number, order_id, created, status, updated_at 
                        FROM bs_coupons ORDER BY id ASC");
    $rows = [];
    while ($row = $dbc->Fetch($rst)) {
        $rows[] = [
            "id" => intval($row['id']),
            "number" => $row['number'],
            "order_id" => $row['order_id'],
            "created" => $row['created'],
            "status" => intval($row['status']),
            "updated_at" => $row['updated_at']
        ];
    }
    echo json_encode([
        "success" => true,
        "data" => $rows,
        "total" => count($rows)
    ]);
}

function getCouponStats($dbc)
{
    $rst = $dbc->Query("SELECT 
                            COUNT(*) total,
                            SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) active,
                            SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) used,
                            SUM(CASE WHEN order_id IS NOT NULL AND order_id!='' THEN 1 ELSE 0 END) with_orders
                        FROM bs_coupons");
    $row = $dbc->Fetch($rst);
    echo json_encode([
        "success" => true,
        "stats" => [
            "total" => intval($row['total']),
            "active" => intval($row['active']),
            "used" => intval($row['used']),
            "with_orders" => intval($row['with_orders']),
            "unused" => intval($row['active'])
        ]
    ]);
}

function findCouponByOrderId($dbc, $order_id)
{
    $order_id = trim($order_id);
    if ($order_id === '') {
        throw new Exception("กรุณาระบุ Order ID");
    }

    $safe_order = addslashes($order_id);
    $sql = "SELECT id, number, order_id, created, status 
            FROM bs_coupons 
            WHERE order_id = '$safe_order' 
            ORDER BY id ASC";
    $rst = $dbc->Query($sql);

    $rows = [];
    while ($row = $dbc->Fetch($rst)) {
        $rows[] = [
            "id"       => intval($row['id']),
            "number"   => $row['number'],
            "order_id" => $row['order_id'],
            "created"  => $row['created'],
            "status"   => intval($row['status'])
        ];
    }

    echo json_encode([
        "success" => true,
        "data"    => $rows,
        "total"   => count($rows)
    ]);
}

function unusedCoupon($dbc, $coupon_id)
{
    if ($coupon_id <= 0) {
        throw new Exception("ไม่พบ Coupon ID");
    }

    $rst = $dbc->Query("SELECT id, status FROM bs_coupons WHERE id=$coupon_id");
    $row = $dbc->Fetch($rst);
    if (!$row) {
        throw new Exception("Coupon ID $coupon_id ไม่พบในระบบ");
    }
    if ($row['status'] == 1) {
        throw new Exception("Coupon ID $coupon_id ยังไม่ได้ใช้งาน");
    }

    $ok = $dbc->Query("UPDATE bs_coupons 
                       SET order_id=NULL, status=1 
                       WHERE id=$coupon_id");
    if ($ok) {
        echo json_encode([
            "success" => true,
            "message" => "Coupon ID $coupon_id ถูกคืนสถานะเรียบร้อยแล้ว"
        ]);
    } else {
        throw new Exception("ไม่สามารถคืนสถานะ Coupon ID $coupon_id ได้");
    }
}
