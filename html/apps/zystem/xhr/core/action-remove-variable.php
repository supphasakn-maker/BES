<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	$variable = $dbc->GetRecord("os_variable","*","id=".$_POST['id']);
	$dbc->Delete("os_variable","id=".$_POST['id']);
	$os->save_log(0,$_SESSION['auth']['user_id'],"variable-delete",$_POST['id'],array("variable" => $variable));
	
	$dbc->Close();
?>