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
	
		$adjust = $dbc->GetRecord("bs_adjust_cost","*","id=".$_POST['id']);
	
		$dbc->Delete("bs_adjust_cost","id=".$_POST['id']);
		$dbc->Update("bs_purchase_spot",array("#adjust_id"=>"NULL"),"adjust_id=".$_POST['id']);
		$dbc->Update("bs_sales_spot",array("#adjust_id"=>"NULL"),"adjust_id=".$_POST['id']);
		
		$os->save_log(0,$_SESSION['auth']['user_id'],"adjust-delete",$id,array("adjusts" => $adjust));



	$dbc->Close();
?>
