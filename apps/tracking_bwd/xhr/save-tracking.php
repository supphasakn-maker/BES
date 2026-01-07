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

    $VERSION = 'save_tracking_v1';

    if (empty($_SESSION['auth']['user_id'])) {
        throw new Exception('คุณหมดเวลาการใช้งานแล้ว โปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
    }

    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);

    if (empty($input['tracking_data']) || !is_array($input['tracking_data'])) {
        throw new Exception('ข้อมูล Tracking ไม่ถูกต้อง');
    }

    $dbc->Query("START TRANSACTION");

    $updated_count = 0;
    $tracking_details = [];

    foreach ($input['tracking_data'] as $item) {
        $box_id = intval($item['box_id']);
        $tracking = trim($item['tracking']);

        if ($dbc->Update("bs_orders_bwd", array(
            "Tracking" => $tracking
        ), "id=" . $box_id)) {
            $updated_count++;

            $tracking_details[] = array(
                'box_id' => $box_id,
                'tracking' => $tracking
            );
        }
    }

    $dbc->Query("COMMIT");

    $os->save_log(
        0,
        $_SESSION['auth']['user_id'],
        "save-tracking",
        0,
        array(
            "version" => $VERSION,
            "updated_count" => $updated_count,
            "tracking_details" => $tracking_details
        )
    );

    echo json_encode(array(
        'success' => true,
        'version' => $VERSION,
        'file' => __FILE__,
        'msg' => 'บันทึก Tracking เรียบร้อยแล้ว (' . $updated_count . ' กล่อง)',
        'updated_count' => $updated_count
    ));
} catch (Exception $e) {
    if (isset($dbc)) {
        @$dbc->Query("ROLLBACK");
    }
    echo json_encode(array(
        'success' => false,
        'version' => 'save_tracking_v1',
        'file' => __FILE__,
        'msg' => 'Error: ' . $e->getMessage()
    ));
    error_log('Save tracking error: ' . $e->getMessage() . ' @' . __FILE__);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
