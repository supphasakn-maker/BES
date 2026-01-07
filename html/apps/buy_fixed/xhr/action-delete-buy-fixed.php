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

$item_id = $_POST['item'];

$spot = $dbc->GetRecord("bs_purchase_buyfix", "*", "id=" . $item_id);

if ($spot && !empty($spot['img'])) {
    $uploadDir = '../../../binary/purchase/';
    $imagePath = $uploadDir . $spot['img'];


    if (file_exists($imagePath)) {

        if (unlink($imagePath)) {

            $os->save_log(0, $_SESSION['auth']['user_id'], "buy_fixed-delete-image", $item_id, array("image_deleted" => $spot['img']));
            error_log("Image deleted successfully: " . $imagePath);
        } else {

            $os->save_log(0, $_SESSION['auth']['user_id'], "buy_fixed-delete-image-failed", $item_id, array("image_to_delete" => $spot['img'], "reason" => "unlink_failed"));
            error_log("Failed to delete image: " . $imagePath);
        }
    } else {

        $os->save_log(0, $_SESSION['auth']['user_id'], "buy_fixed-delete-image-not-found", $item_id, array("image_to_delete" => $spot['img']));
        error_log("Image file not found: " . $imagePath);
    }
} else {

    $os->save_log(0, $_SESSION['auth']['user_id'], "buy_fixed-delete-no-image-data", $item_id, array("spot_data" => $spot));
    if (!$spot) {
        error_log("Record not found for deletion: " . $item_id);
    } else {
        error_log("Image column is empty for record: " . $item_id);
    }
}

$dbc->Delete("bs_purchase_buyfix", "id=" . $item_id);
$os->save_log(0, $_SESSION['auth']['user_id'], "buy_fixed-delete-db", $item_id, array("spot_data" => $spot));

$dbc->Close();
