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
	
	$unremovable = array();
	
	foreach($_POST['items'] as $item){
		$contact = $dbc->GetRecord("os_contacts","*","id=".$item);
		$address = $dbc->GetRecord("os_address","*","contact=".$contact['id']);
		
		if($dbc->HasRecord("os_users","contact=".$contact['id'])){
			array_push($unremovable,$contact['id']);
		}else{
			$dbc->Delete("os_contacts","id=".$item);
			$dbc->Delete("os_address","contact=".$item);
			$os->save_log(0,$_SESSION['auth']['user_id'],"contact-delete",$contact['id'],array("contacts" => $contact,"address" => $address));
		}
	}
			
	echo json_encode($unremovable);
	
		
	
	$dbc->Close();
?>