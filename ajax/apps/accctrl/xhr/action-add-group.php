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
	
	
	if($dbc->HasRecord("os_groups","name = '".$_POST['name']."' AND account = ".$_POST['account'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Group Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'#account' => $_POST['account']
		);
		
		if($dbc->Insert("os_groups",$data)){
			$group_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $group_id
			));
			
			$group = $dbc->GetRecord("os_groups","*","id=".$group_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"group-add",$group_id,array("groups" => $group));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>