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
	
	if($dbc->HasRecord("os_users","name = '".$_POST['username']."' AND id != ".$_POST['user_id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Username is already exist.'
		));
	}else{
		$data = array(
			'title' => $_POST['title'],
			'name' => $_POST['first'],
			'surname' => $_POST['surname'],
			'nickname' => $_POST['nickname'],
			'gender' => $_POST['gender'],
			'email' => $_POST['email'],
			'phone' => $_POST['phone'],
			'mobile' => $_POST['mobile'],
			'#updated' => "NOW()"
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
		$fulladdress = $os->load_fulladdress($_POST['address_id']);
		$dbc->Update("os_address",array("fulladdress"=>$fulladdress),"id=".$_POST['address_id']);
		
		$display_name = $_POST['username'];
		if($_POST['first']!="")$display_name = $_POST['first'];
		if($_POST['surname']!="")$display_name .= " ".$_POST['surname'];
		
		$data = array(
			'name' => $_POST['username'],
			'display' => $display_name,
			'#updated' => "NOW()",
			'#gid' => $_POST['gid']
		);
		if($_POST['password']!="")$data['#password'] = "SHA2('".$_POST['password']."', 224)";
		$dbc->Update("os_users", $data,"id=".$_POST['user_id']);
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> "Passed"
		));
		
		$user = $dbc->GetRecord("os_users","*","id=".$_POST['user_id']);
		$contact = $dbc->GetRecord("os_contacts","*","id=".$_POST['contact_id']);
		$address = $dbc->GetRecord("os_address","*","id=".$_POST['address_id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"user-edit",$_POST['user_id'],array("users" => $user,"contacts" => $contact,"address" => $address));
	}
	
	$dbc->Close();
?>