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
			'msg'=>'Please insert Firstname!'
		));
	}else if($dbc->HasRecord("os_contacts","name = '".$_POST['name']."' AND surname = '".$_POST['surname']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'This name is already exist.'
		));
	}else{
		
		$json = array(
			'skype' => $_POST['skype'],
			'facebook' => $_POST['facebook'],
			'line' => $_POST['line'],
			'google' => $_POST['google']
		);
		
		$data = array(
			'#id' => "DEFAULT",
			'title' => $_POST['title'],
			'name' => $_POST['name'],
			'surname' => $_POST['surname'],
			'nickname' => $_POST['nickname'],
			'gender' => $_POST['gender'],
			'email' => $_POST['email'],
			'phone' => $_POST['phone'],
			'mobile' => $_POST['mobile'],
			'citizen_id' => $_POST['citizen_id'],
			'skype' => $_POST['skype'],
			'facebook' => $_POST['facebook'],
			'google' => $_POST['google'],
			'line' => $_POST['line'],
			'#created' => "NOW()",
			'#updated' => "NOW()",
			'status' => 1,
			'data' => json_encode($json)
		);
		
		if($_POST['dob']==""){
			$data['#dob'] = "NULL";
		}else{
			$data['dob'] = $_POST['dob'];
		}
		
		$dbc->Insert("os_contacts", $data);
		$contact_id = $dbc->GetID();
		
		$data = array(
			'#id' => "DEFAULT",
			'address' => $_POST['address'],
			'#country' => $_POST['country'],
			'#city' => $_POST['city'],
			'#district' => $_POST['district'],
			'#subdistrict' => $_POST['subdistrict'],
			'postal' => $_POST['postal'],
			'#created' => "NOW()",
			'#updated' => "NOW()",
			'#contact' => $contact_id,
			'#organization' => 'NULL',
			'priority' => 1
		);
		
		if($dbc->Insert("os_address", $data)){
			$address_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $contact_id
			));
			
			$address = $dbc->GetRecord("os_address","*","id=".$address_id);
			$country = $dbc->GetRecord("db_countries","*","id=".$address['country']);
			$city = $dbc->GetRecord("db_cities","*","id=".$address['city']);
			$district = $dbc->GetRecord("db_districts","*","id=".$address['district']);
			$subdistrict = $dbc->GetRecord("db_subdistricts","*","id=".$address['subdistrict']);
			$fulladdress = $address['address'].' '.$subdistrict['name'].' '.$district['name'].' '.$city['name'].' '.$address['postal'];
			$dbc->Update("os_address",array("fulladdress"=>$fulladdress),"id=".$address_id);
			
			$contact = $dbc->GetRecord("os_contacts","*","id=".$contact_id);
			$address = $dbc->GetRecord("os_address","*","id=".$address_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"contact-add",$contact_id,array("os_contacts" => $contact,"address" => $address));
			
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>