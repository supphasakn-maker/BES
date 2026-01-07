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


	if($dbc->HasRecord("bs_employees","fullname = '".$_POST['fullname']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Employee Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'fullname' => $_POST['fullname'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'nickname' => $_POST['nickname'],
			'signature' => '',
			'#status' => 1,
			'#user' => $_POST['user'],
			'#department' => $_POST['department']
		);
		
		if($_POST['dob']!=""){
			$data['dob'] = $_POST['dob'];
		}else{
			$data['#dob'] = "NULL";
		}

		if($dbc->Insert("bs_employees",$data)){
			$employee_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $employee_id
			));

			$employee = $dbc->GetRecord("bs_employees","*","id=".$employee_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"employee-add",$employee_id,array("bs_employees" => $employee));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
