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
		$spot = $dbc->GetRecord("bs_sales_spot","*","id=".$_POST['item']);
		$dbc->Delete("bs_sales_spot","id=".$_POST['item']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"spot-delete",$id,array("bs_sales_spot" => $spot));
		
	}else{
		foreach($_POST['items'] as $item){
			$spot = $dbc->GetRecord("bs_sales_spot","*","id=".$item);
			$dbc->Delete("bs_sales_spot","id=".$item);
			$os->save_log(0,$_SESSION['auth']['user_id'],"spot-delete",$id,array("bs_sales_spot" => $spot));
		}
	}

	

	$dbc->Close();
?>
