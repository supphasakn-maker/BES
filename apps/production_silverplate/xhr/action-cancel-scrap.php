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

$scrap_id = intval($_POST['scrap_id']);
$today = date("Y-m-d");

// ดึงข้อมูล scrap item ที่จะยกเลิก (เศษหลักที่ถูก Combined)
$scrap = $dbc->GetRecord("bs_scrap_items", "*", "id=" . $scrap_id);

if (!$scrap) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่พบข้อมูล Scrap Item (ID: ' . $scrap_id . ')'
    ));
    exit;
}

// ตรวจสอบว่าถูก Combined แล้ว (status != 0)
if ($scrap['status'] == 0) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่สามารถยกเลิกได้ เนื่องจากยังไม่ได้ถูก Combined (status = 0)'
    ));
    exit;
}

// ตรวจสอบว่า created เป็นวันนี้หรือไม่
$created_date = date("Y-m-d", strtotime($scrap['created']));
if ($created_date != $today) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่สามารถยกเลิกย้อนหลังได้ สามารถยกเลิกได้เฉพาะภายในวันที่สร้างเท่านั้น (สร้างวันที่: ' . date('d/m/Y', strtotime($created_date)) . ')'
    ));
    exit;
}

// 1. Update เศษลูกทั้งหมดที่มี parent = scrap_id: set parent = NULL และ status = 0
$sql_update = "UPDATE bs_scrap_items SET parent = NULL, status = 0 WHERE parent = " . $scrap_id;
$dbc->Query($sql_update);

// บันทึก log
$os->save_log(
    0,
    $_SESSION['auth']['user_id'],
    "bs_scrap_items-undo-combine-all",
    $scrap_id,
    array(
        "action" => "undo_combine",
        "parent_id" => $scrap_id,
        "affected_children" => "updated to parent=NULL, status=0"
    )
);

// 2. ลบเศษหลัก (id = scrap_id)
if ($dbc->Delete("bs_scrap_items", "id=" . $scrap_id)) {
    // บันทึก log การยกเลิก
    $os->save_log(
        0,
        $_SESSION['auth']['user_id'],
        "bs_scrap_items-undo",
        $scrap_id,
        array(
            "bs_scrap_items" => $scrap
        )
    );

    echo json_encode(array(
        'success' => true,
        'msg' => 'ยกเลิก Combined สำเร็จ!'
    ));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่สามารถลบเศษหลักได้'
    ));
}
