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
	
	if(is_array($_POST['item'])){
		$unremovable = array();
		foreach($_POST['item'] as $item){
			$address = $dbc->GetRecord("os_address","*","id=".$item);
			if($address['priority']=="1"){
				array_push($unremovable,$address['id']);
			}else{
				$dbc->Delete("os_address","id=".$item);
				$os->save_log(0,$_SESSION['auth']['user_id'],"contact-delete",$address['id'],array("address" => $address));
			}
		}
		echo json_encode($unremovable);
	}else{
		$address = $dbc->GetRecord("os_address","*","id=".$_POST['item']);
		if($address['priority']=="1"){
			array_push($unremovable,$address['id']);
		}else{
			$dbc->Delete("os_address","id=".$_POST['item']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"contact-delete",$address['id'],array("address" => $address));
		}
		echo json_encode(array());
		
	}
	
	$dbc->Close();
?>