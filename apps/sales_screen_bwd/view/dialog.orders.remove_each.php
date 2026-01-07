<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);


if (!isset($_POST['id'])) {
    error_log("ERROR: No ID in modal request");
    echo "<div class='alert alert-danger'>Error: No order ID provided</div>";
    exit;
}

$order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_POST['id']);

if (!$order) {
    echo "<div class='alert alert-danger'>Error: Order not found with ID: " . $_POST['id'] . "</div>";
    exit;
}

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_remove_each_orders", "Cancle Order");
$modal->initiForm("form_remove_eachorder");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-danger", "Remove", "fn.app.sales_screen_bwd.multiorder.remove_each()")
));

$modal->SetVariable(array(
    array("id", $order['id'])
));

$blueprint = array(
    array(
        array(
            "name" => "remove_reason",
            "caption" => "เหตุผลที่ยกเลิกคำสั่งซื้อ",
            "type" => "textarea",
            "required" => true
        )
    )
);

$modal->SetBlueprint($blueprint);

$modal->EchoInterface();
$dbc->Close();
