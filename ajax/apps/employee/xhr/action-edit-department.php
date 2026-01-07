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

	if($dbc->HasRecord("bs_departments","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Department Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name']
		);

		if($dbc->Update("bs_departments",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$department = $dbc->GetRecord("bs_departments","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"department-edit",$_POST['id'],array("bs_departments" => $department));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
