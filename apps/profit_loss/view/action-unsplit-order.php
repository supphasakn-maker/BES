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

// Include OrderSplitManager
include_once "../include/order_split_manager.php";

header('Content-Type: application/json');

if (empty($_SESSION['auth']['user_id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
    ));
    exit();
} else if (empty($_POST['parent'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่พบ Parent Order ID'
    ));
} else {
    $parent = intval($_POST['parent']);

    // หา split records ทั้งหมดที่มี parent นี้
    $split_records = $dbc->GetRecord("bs_orders_profit", "*", "parent=" . $parent . " AND is_split=1", "split_sequence ASC");

    if (empty($split_records)) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'ไม่พบ Split Records ที่เกี่ยวข้อง'
        ));
    } else {
        // หา original record (split_sequence = 1)
        $original_record = null;
        foreach ($split_records as $record) {
            if ($record['split_sequence'] == 1) {
                $original_record = $record;
                break;
            }
        }

        if (!$original_record) {
            echo json_encode(array(
                'success' => false,
                'msg' => 'ไม่พบ Original Record'
            ));
        } else {
            // เก็บข้อมูลก่อน unsplit สำหรับ log
            $before_unsplit = array(
                'split_count' => count($split_records),
                'records' => $split_records
            );

            // ทำการ unsplit
            $splitManager = new OrderSplitManager($dbc);
            $result = $splitManager->unsplitOrder($original_record['id']);

            if (!$result['success']) {
                echo json_encode(array(
                    'success' => false,
                    'msg' => $result['message']
                ));
            } else {
                // บันทึก log
                $os->save_log(0, $_SESSION['auth']['user_id'], "order-unsplit", $original_record['id'], array(
                    "parent" => $parent,
                    "original_record" => $original_record,
                    "before_unsplit" => $before_unsplit
                ));

                echo json_encode(array(
                    'success' => true,
                    'msg' => 'Unsplit Order สำเร็จ รวม ' . count($split_records) . ' รายการกลับเป็น 1 รายการ',
                    'data' => array(
                        'parent' => $parent,
                        'original_id' => $original_record['id'],
                        'merged_count' => count($split_records)
                    )
                ));
            }
        }
    }
}

$dbc->Close();
