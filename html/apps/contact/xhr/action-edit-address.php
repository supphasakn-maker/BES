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
	
	$data = array(
		'address' => $_POST['address'],
		'postal' => $_POST['postal'],
		'#updated' => "NOW()",
		'remark' => $_POST['remark']
	);
	
	if(isset($_POST['country']))$data['#country']=$_POST['country'];
	if(isset($_POST['city']))$data['#city']=$_POST['city'];
	if(isset($_POST['subdistrict']))$data['#subdistrict']=$_POST['subdistrict'];
	if(isset($_POST['district']))$data['#district']=$_POST['district'];
	
	$dbc->Update("os_address", $data,"id=".$_POST['id']);
	$address = $dbc->GetRecord("os_address","*","id=".$_POST['id']);
	$country = $dbc->GetRecord("db_countries","*","id=".$address['country']);
	$city = $dbc->GetRecord("db_cities","*","id=".$address['city']);
	$district = $dbc->GetRecord("db_districts","*","id=".$address['district']);
	$subdistrict = $dbc->GetRecord("db_subdistricts","*","id=".$address['subdistrict']);
	$fulladdress = $address['address'].' '.$subdistrict['name'].' '.$district['name'].' '.$city['name'].' '.$address['postal'];
	$dbc->Update("os_address",array("fulladdress"=>$fulladdress),"id=".$_POST['id']);
	
	$address = $dbc->GetRecord("os_address","*","id=".$_POST['id']);
	$os->save_log(0,$_SESSION['auth']['user_id'],"address-edit",$_POST['id'],array("address" => $address));
	echo json_encode(array("success"=>true));
	
	$dbc->Close();
?>