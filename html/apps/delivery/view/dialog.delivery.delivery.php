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

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
		$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $this->param['id']);
		echo '<form name="form_deliverydelivery">';
		echo '<input type="hidden" value="' . $this->param['id'] . '">';
		echo 'Are tour sure to confirm delivery?';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_delivery_delivery", "Delivery Delivery");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Delivery", "fn.app.delivery.delivery.delivery()")
));
$modal->EchoInterface();

$dbc->Close();
