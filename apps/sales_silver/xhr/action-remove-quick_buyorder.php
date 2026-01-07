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
	
	$dd = date('Y-m-d');
    $time = date("H:i");


		$spot = $dbc->GetRecord("bs_orders_buy","*","id=".$_POST['item']);

		$dbc->Delete("bs_orders_buy","id=".$_POST['item']);
		$dbc->Delete("bs_stock_silver","customer_po=".$_POST['item']);

		$os->save_log(0,$_SESSION['auth']['user_id'],"bs_orders_buy_quickbuysilver-delete",$id,array("bs_orders_buy" => $spot));


	$dbc->Close();
?>
