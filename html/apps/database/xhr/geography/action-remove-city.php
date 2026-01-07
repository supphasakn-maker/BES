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
	
	foreach($_REQUEST['items'] as $item){
		$city = $dbc->GetRecord("db_cities","*","id=".$item);
		$dbc->Delete("db_cities","id=".$item);
		$os->save_log(0,$_SESSION['auth']['user_id'],"city-delete",$id,array("db_cities" => $city));
	}
	
	$dbc->Close();
?>