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
	
	if($dbc->HasRecord("os_contacts","name = '".$_POST['name']."' AND surname = '".$_POST['surname']."' AND id != ".$_POST['contact_id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Contact Name is already exist.'
		));
	}else{
		$json = array(
			'skype' => $_POST['skype'],
			'facebook' => $_POST['facebook'],
			'line' => $_POST['line'],
			'google' => $_POST['google']
		);
		
		
		$data = array(			
			'title' => $_POST['title'],
			'name' => $_POST['name'],
			'surname' => $_POST['surname'],
			'nickname' => $_POST['nick'],
			'gender' => $_POST['gender'],
			'email' => $_POST['email'],
			'skype' => $_POST['skype'],
			'facebook' => $_POST['facebook'],
			'line' => $_POST['line'],
			'google' => $_POST['google'],
			'citizen_id' => $_POST['citizen_id'],
			'phone' => $_POST['phone'],
			'mobile' => $_POST['mobile'],
			'#updated' => "NOW()",
			'data' => json_encode($json)
		);
		if($_POST['dob']==""){
			$data['#dob'] = "NULL";
		}else{
			$data['dob'] = $_POST['dob'];
		}
		$dbc->Update("os_contacts", $data,"id=".$_POST['contact_id']);
		
		$data = array(
			'address' => $_POST['address'],
			'postal' => $_POST['postal'],
			'#updated' => "NOW()"
		);
		
		if(isset($_POST['country']))$data['#country']=$_POST['country'];
		if(isset($_POST['city']))$data['#city']=$_POST['city'];
		if(isset($_POST['subdistrict']))$data['#subdistrict']=$_POST['subdistrict'];
		if(isset($_POST['district']))$data['#district']=$_POST['district'];
		
		$dbc->Update("os_address", $data,"id=".$_POST['address_id']);
		
		
		$address = $dbc->GetRecord("os_address","*","id=".$_POST['address_id']);
		$country = $dbc->GetRecord("db_countries","*","id=".$address['country']);
		$city = $dbc->GetRecord("db_cities","*","id=".$address['city']);
		$district = $dbc->GetRecord("db_districts","*","id=".$address['district']);
		$subdistrict = $dbc->GetRecord("db_subdistricts","*","id=".$address['subdistrict']);
		$fulladdress = $address['address'].' '.$subdistrict['name'].' '.$district['name'].' '.$city['name'].' '.$address['postal'];
		$dbc->Update("os_address",array("fulladdress"=>$fulladdress),"id=".$_POST['address_id']);
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> "Passed"
		));
		
		$contact = $dbc->GetRecord("os_contacts","*","id=".$_POST['contact_id']);
		$address = $dbc->GetRecord("os_address","*","id=".$_POST['address_id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"contact-edit",$_POST['contact_id'],array("contacts" => $contact,"address" => $address));
	
		
	}
	
	$dbc->Close();
?>