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

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'No order ID provided',
        'error_type' => 'missing_id'
    ]);
    $dbc->Close();
    exit;
}

$order_id = intval($_POST['id']);

$order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);

if (!$order) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Order not found',
        'error_type' => 'order_not_found'
    ]);
    $dbc->Close();
    exit;
}

/* =========================
 * Permission Check
 * ========================= */
$user_id   = intval($_SESSION['auth']['user_id'] ?? 0);
$user_data = $dbc->GetRecord("os_users", "*", "id=" . $user_id);

$user_gid  = ($user_data && isset($user_data['gid'])) ? intval($user_data['gid']) : null;
$can_delete = in_array($user_gid, [1, 3], true);

if (!$can_delete) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'คุณไม่มีสิทธิ์ในการยกเลิกออเดอร์',
        'error_type' => 'permission_denied',
        'user_gid' => $user_gid
    ]);
    $dbc->Close();
    exit;
}

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_remove_order_orders", "Cancel Order");
$modal->initiForm("form_remove_order_orders");
$modal->setExtraClass("modal-lg");

$modal->setButton([
    ["close",  "btn-secondary", "Dismiss"],
    ["action", "btn-danger",    "Remove", "fn.app.sales_screen_bwd.multiorder.remove_order()"]
]);

$modal->SetVariable([
    ["id", $order['id']]
]);

$blueprint = [
    [
        [
            "name"     => "remove_reason",
            "caption"  => "เหตุผลที่ยกเลิกคำสั่งซื้อ",
            "type"     => "textarea",
            "required" => true
        ]
    ]
];

$modal->SetBlueprint($blueprint);


$modal->EchoInterface();

$dbc->Close();
