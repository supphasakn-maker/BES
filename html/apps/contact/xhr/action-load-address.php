<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$address = $dbc->GetRecord("os_address","*","id=".$_REQUEST['id']);
	$country = $dbc->GetRecord("db_countries","*","id=".$address['country']);
	$city = $dbc->GetRecord("db_cities","*","id=".$address['city']);
	$district = $dbc->GetRecord("db_districts","*","id=".$address['district']);
	$subdistrict = $dbc->GetRecord("db_subdistricts","*","id=".$address['subdistrict']);
	
	echo json_encode(array(
		"id" 			=> $address['id'],
		"address" 		=> $address['address'],
		"country" 		=> $country['name'],
		"city" 			=> $city['name'],
		"district" 		=> $district['name'],
		"subdistrict" 	=> $subdistrict['name'],
		"postal" 		=> $address['postal'],
		"comment" 		=> $address['comment'],
		"created" 		=> $address['created'],
		"updated" 		=> $address['updated'],
		"fulladdress"	=> $address['address']."\n".$subdistrict['name'].' '.$district['name'].' '.$city['name'].' '.$address['postal']
	));
	
	$dbc->Close();
?>