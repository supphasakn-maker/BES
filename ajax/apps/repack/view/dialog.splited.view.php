<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/nebulaos.php";
	include_once "../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new nebulaos($dbc);

	class myModel extends imodal{
		function body(){
			global $os;
			$dbc = $this->dbc;
			$packing = $dbc->GetRecord("bs_packing_items","*","id=".$this->param['id']);
			
			$os->create_table_from_record($packing,"table table-sm table-bordered");
			
			//$sql = "SELECT * FROM bs_mapping_silver_orders WHERE order_id = order.id";
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_packing_split","Spltied Detail");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
