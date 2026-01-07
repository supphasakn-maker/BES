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

$scrap_id = $_POST['scrap_id'];

$scrap = $dbc->GetRecord("bs_scrap_items", "*", "id=" . $scrap_id);

if (!$scrap) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่พบข้อมูล Scrap Item'
    ));
    exit;
}

if ($scrap['status'] != 0) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่สามารถยกเลิกได้ เนื่องจากถูกนำไปใช้แล้ว'
    ));
    exit;
}

$production_id = $scrap['production_id'];
$weight = $scrap['weight_actual'];
$pack_name = $scrap['pack_name'];

if ($pack_name == "เม็ดเสียรอการผลิต") {
    $data_revert = array(
        '#weight_out_safe' => -$weight,
        '#weight_out_total' => -$weight,
        '#weight_margin' => -$weight
    );
} else {
    $data_revert = array(
        '#weight_out_refine' => -$weight
    );
}

if (!$dbc->Update("bs_productions", $data_revert, "id=" . $production_id)) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่สามารถอัพเดท Production ได้'
    ));
    exit;
}

$child_scraps = $dbc->GetRecord("bs_scrap_items", "*", "parent=" . $scrap_id . " AND status=0");

if ($child_scraps && count($child_scraps) > 0) {
    foreach ($child_scraps as $child) {
        $dbc->Update("bs_scrap_items", array("parent" => "NULL"), "id=" . $child['id']);

        $os->save_log(
            0,
            $_SESSION['auth']['user_id'],
            "bs_scrap_items-update-parent",
            $child['id'],
            array("old_parent" => $scrap_id, "new_parent" => null)
        );
    }
}

if ($dbc->Delete("bs_scrap_items", "id=" . $scrap_id)) {
    $os->save_log(
        0,
        $_SESSION['auth']['user_id'],
        "bs_scrap_items-cancel",
        $scrap_id,
        array("bs_scrap_items" => $scrap)
    );

    echo json_encode(array(
        'success' => true,
        'msg' => 'ยกเลิกเรียบร้อยแล้ว'
    ));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => 'ไม่สามารถลบข้อมูลได้'
    ));
}
