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
	
	$dbc->Query("TRUNCATE `bs_mapping_silvers`");
	$dbc->Query("TRUNCATE `bs_mapping_silver_orders`");
	$dbc->Query("TRUNCATE `bs_mapping_silver_purchases`");
	$dbc->Query("TRUNCATE `bs_mapping_usd`");
	$dbc->Query("TRUNCATE `bs_mapping_usd_purchases`");
	$dbc->Query("TRUNCATE `bs_mapping_usd_spots`");


	echo json_encode(array(
		'success'=>true
	));
	

	$dbc->Close();
?>
