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
		$static = $dbc->GetRecord("bs_finance_static_values","*","id=".$item);
		$dbc->Delete("bs_finance_static_values","id=".$item);
		$os->save_log(0,$_SESSION['auth']['user_id'],"static-delete",$id,array("statics" => $static));
	}

	$dbc->Close();
?>
