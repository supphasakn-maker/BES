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
		$daily = $dbc->GetRecord("bs_smg_stx_daily","*","id=".$item);
		$dbc->Delete("bs_smg_stx_daily","id=".$item);
		$os->save_log(0,$_SESSION['auth']['user_id'],"bs_smg_stx_daily-delete",$id,array("dailys" => $daily));
	}

	$dbc->Close();
?>
