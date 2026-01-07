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
			'#id' => "DEFAULT",
			'name' => $_POST['name']
		);

		if($dbc->Insert("bs_departments",$data)){
			$department_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $department_id
			));

			$department = $dbc->GetRecord("bs_departments","*","id=".$department_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"department-add",$department_id,array("bs_departments" => $department));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
