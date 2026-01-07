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
	
	if($_POST['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert Organization Name!'
		));
	}else if($dbc->HasRecord("os_organizations","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'This name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'code' => "",
			'name' => $_POST['name'],
			'email' => $_POST['email'],
			'phone' => $_POST['phone'],
			'fax' => $_POST['fax'],
			'website' => $_POST['website'],
			'#created' => "NOW()",
			'#updated' => "NOW()",
			'type' => $_POST['type'],
			'tax_id' => $_POST['tax_id'],
			'logo' => '',
			'branch' => $_POST['branch']
		);
		
		if($_POST['parent']=="0"){
			$data['#parent'] = "NULL";
		}else{
			$data['#parent'] = $_POST['parent'];
		}
		
		$dbc->Insert("os_organizations",$data);
		$org_id = $dbc->GetID();
		
		$data = array(
			'#id' => "DEFAULT",
			'address' => $_POST['address'],
			'country' => $_POST['country'],
			'city' => $_POST['city'],
			'district' => $_POST['district'],
			'subdistrict' => $_POST['subdistrict'],
			'postal' => $_POST['postal'],
			'#created' => "NOW()",
			'#updated' => "NOW()",
			'#contact' => 'NULL',
			'#organization' => $org_id,
			'priority' => 1
		);
		
		$dbc->Insert("os_address", $data);
		$address_id = $dbc->GetID();
		
		$fulladdress = $os->load_fulladdress($address_id);
		$dbc->Update("os_address",array("fulladdress"=>$fulladdress),"id=".$address_id);
		
		$organization = $dbc->GetRecord("os_organizations","*","id=".$org_id);
		$address = $dbc->GetRecord("os_address","*","organization=".$org_id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"organization-add",$org_id,array("organizations" => $organization,"address" => $address));
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> $org_id
		));
		
	}
	
	$dbc->Close();
?>