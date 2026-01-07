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

	foreach($_POST['items'] as $item){
		$trading = $dbc->GetRecord("bs_trade_spot","*","id=".$item);
		$dbc->Delete("bs_trade_spot","id=".$item);
		
		$dbc->Update("bs_sales_spot",array(
			"#status" => 1,
			"#trade_id" => "NULL"
		),"trade_id=".$item);
					
		$dbc->Update("bs_purchase_spot",array(
			"#status" => 1,
			"#trade_id" =>  "NULL"
		),"trade_id=".$item);
		
		
		$os->save_log(0,$_SESSION['auth']['user_id'],"trading-delete",$id,array("tradings" => $trading));
	}

	$dbc->Close();
?>
