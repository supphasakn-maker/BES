<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_POST['id']);

class myModel extends imodal
{
    function body()
    {
        $dbc = $this->dbc;
        $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $this->param['id']);
        if ($order['lock_status'] == 0) {
            echo "Are you sure to lock delivery date?";
        } else {
            echo "Are you sure to unlock delivery date?";
        }
        echo '<form name="form_lockorder"><input type="hidden" name="id" value="' . $order['id'] . '"></form>';
    }
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_lock_order", "Lock Order");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-danger", ($order['lock_status'] == 0) ? "Lock" : "Unlock", "fn.app.sales_screen_bwd_2.multiorder.lock()")
));
$modal->EchoInterface();

$dbc->Close();
