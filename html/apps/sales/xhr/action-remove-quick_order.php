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
		$quick_order = $dbc->GetRecord("bs_quick_orders","*","id=".$_POST['item']);
		$dbc->Delete("bs_quick_orders","id=".$_POST['item']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"quick_order-delete",$id,array("bs_quick_orders" => $quick_order));
		
	}else{
		$quick_order = $dbc->GetRecord("bs_quick_orders","*","id=".$item);
		$dbc->Delete("bs_quick_orders","id=".$item);
		$os->save_log(0,$_SESSION['auth']['user_id'],"quick_order-delete",$id,array("quick_orders" => $quick_order));
	}

	$dbc->Close();
?>
