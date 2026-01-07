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
	
	
	function load_user($dbc,$id){
		$user = $dbc->GetRecord("users","*","id=".$id);
		$group = $dbc->GetRecord("groups","*","id=".$user['gid']);
		$contact = $dbc->GetRecord("contacts","*","id=".$user['contact']);
		$address = $dbc->GetRecord("address","*","contact=".$contact['id']);
		$setting = json_decode($user['setting'],true);
		
		if($contact['avatar']==""){
			$avatar = "img/default/user.png";
		}else{
			$avatar = $contact['avatar'];
		}
			
		$display_name = $user['name'];
		if($contact['name']!="")$display_name = $contact['name'];
		if($contact['surname']!="")$display_name .= " ".$contact['surname'];
		
		return array(
			"id" => intval($user['id']),
			"username" => $user['name'],
			"gid" => $group['id'],
			"group" => $group['name'],
			"name" => $contact['name'],
			"title" => $contact['title'],
			"dob" => $contact['dob'],
			"gender" => $contact['gender'],
			"surname" => $contact['surname'],
			"email" => $contact['email'],
			"phone" => $contact['phone'],
			"mobile" => $contact['mobile'],
			"skype" => $contact['skype'],
			"avatar" => $avatar,
			"display" => $display_name,
			"setting" => $setting,
			"contact" => $contact,
			"address" => $address
		);
	}
	
	if(is_array($_POST['id'])){
		$array = array();
		foreach($_POST['id'] as $id){
			array_push($array,load_user($dbc,$id));
		}
		echo json_encode($array);
	}else{
		echo json_encode(load_user($dbc,$_POST['id']));
	}

	
	$dbc->Close();
?>