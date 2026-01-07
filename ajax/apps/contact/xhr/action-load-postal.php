<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	if(isset($_GET['subdistrict']) && $_GET['subdistrict']!="" && $dbc->HasRecord("db_zipcode_thailand","DISTRICT_ID=".$_GET['subdistrict'])){
		$zipcode = $dbc->GetRecord("db_zipcode_thailand","*","DISTRICT_ID=".$_GET['subdistrict']);
		echo $zipcode['ZIPCODE'];
	}else{
		echo "";
	}
	$dbc->Close();
?>