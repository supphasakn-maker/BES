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

	if($dbc->HasRecord("bs_employees","fullname = '".$_POST['fullname']."' AND id !=".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Employee Name is already exist.'
		));
	}else{
		$data = array(
			'fullname' => $_POST['fullname'],
			'#updated' => 'NOW()',
			'nickname' => $_POST['nickname'],
			'#user' => $_POST['user'],
			'#department' => $_POST['department']
		);
		
		if($_POST['dob']!=""){
			$data['dob'] = $_POST['dob'];
		}else{
			$data['#dob'] = "NULL";
		}

		if($dbc->Update("bs_employees",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$employee = $dbc->GetRecord("bs_employees","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"employee-edit",$_POST['id'],array("bs_employees" => $employee));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
