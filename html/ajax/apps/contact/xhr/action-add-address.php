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
		'#id' => "DEFAULT",
		'address' => $_POST['address'],
		'postal' => $_POST['postal'],
		'#created' => "NOW()",
		'#updated' => "NOW()",
		'priority' => 2,
		'remark' => $_POST['remark']
	);
	
	if(isset($_POST['country']))$data['#country']=$_POST['country'];
	if(isset($_POST['city']))$data['#city']=$_POST['city'];
	if(isset($_POST['subdistrict']))$data['#subdistrict']=$_POST['subdistrict'];
	if(isset($_POST['district']))$data['#district']=$_POST['district'];
	
	if($_POST['type']=="contact"){
		$data['#contact'] = $_POST['id'];
		$data['#organization'] = 'NULL';
	}else{
		$data['#contact'] = 'NULL';
		$data['#organization'] = $_POST['id'];
		
	}
		
	if($dbc->Insert("os_address",$data)){
		$address_id = $dbc->GetID();
		
		$address = $dbc->GetRecord("os_address","*","id=".$address_id);
		$country = $dbc->GetRecord("db_countries","*","id=".$address['country']);
		$city = $dbc->GetRecord("db_cities","*","id=".$address['city']);
		$district = $dbc->GetRecord("db_districts","*","id=".$address['district']);
		$subdistrict = $dbc->GetRecord("db_subdistricts","*","id=".$address['subdistrict']);
		$fulladdress = $address['address'].' '.$subdistrict['name'].' '.$district['name'].' '.$city['name'].' '.$address['postal'];
		$dbc->Update("os_address",array("fulladdress"=>$fulladdress),"id=".$address_id);
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> $address_id
		));
		
		$address = $dbc->GetRecord("os_address","*","id=".$address_id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"address-add",$address_id,array("address" => $address));
		
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Insert Error"
		));
	}
	
	
	$dbc->Close();
?>