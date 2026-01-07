<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	if(isset($_POST['item'])){
		
		$delivery = $dbc->GetRecord("bs_deliveries_bwd","*","id=".$_POST['item']);
		$dbc->Update("bs_orders_bwd",array("#delivery_id"=>"NULL"),"delivery_id=".$_POST['item']);
		$dbc->Delete("bs_deliveries_bwd","id=".$_POST['item']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"bwd-delivery-delete",$id,array("bwd-delivery" => $delivery));
	}else{

		foreach($_POST['items'] as $item){
			$delivery = $dbc->GetRecord("bs_deliveries_bwd","*","id=".$item);
			$dbc->Update("bs_orders_bwd",array("#delivery_id"=>"NULL"),"delivery_id=".$item);
			$dbc->Delete("bs_deliveries_bwd","id=".$item);
			$os->save_log(0,$_SESSION['auth']['user_id'],"bwd-delivery-delete",$id,array("bwd-delivery" => $delivery));
		}
	
	}


	$dbc->Close();
?>
