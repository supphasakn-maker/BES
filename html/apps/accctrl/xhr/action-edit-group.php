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
	
	if($dbc->HasRecord("os_groups","name = '".$_POST['name']."' AND id != ".$_POST['group_id'].' AND account = '.$_POST['account'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Group Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'#updated' => 'NOW()',
			'#account' => $_POST['account']
		);
		
		if($dbc->Update("os_groups",$data,"id=".$_POST['group_id'])){
			echo json_encode(array(
				'success'=>true
			));
			$group = $dbc->GetRecord("os_groups","*","id=".$_POST['group_id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"group-edit",$_POST['group_id'],array("groups" => $group));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	
	$dbc->Close();
?>