<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);
	$order = $dbc->GetRecord("bs_orders","*","id=".$_POST['id']);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			$order = $dbc->GetRecord("bs_orders","*","id=".$this->param['id']);
			echo "Are you sure to make delivery_note for ".$order['code'];
			echo '<form name="form_add_deliveryorder"><input type="hidden" name="id" value="'.$order['id'].'">';
			echo '<input type="date" name="delivery_date" class="form-control">';
			
			echo '</form>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_add_delivery_order","Make Delivery Note");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Make Delivery","fn.app.sales.order.add_delivery()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
