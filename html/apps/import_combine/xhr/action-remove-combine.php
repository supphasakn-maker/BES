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
	
	$import = $dbc->GetRecord("bs_import_usd_splited","*","id=".$_POST['id']);
	$dbc->Delete("bs_import_usd_splited","import_id=".$import['import_id']);
	$os->save_log(0,$_SESSION['auth']['user_id'],"import-combine-delete",$import['import_id'],array("bs_imports" => $import));
	
		
		
	

	$dbc->Close();
?>
