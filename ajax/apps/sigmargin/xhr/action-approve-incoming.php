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

	if($dbc->HasRecord("incomings","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Incoming Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'#updated' => 'NOW()',
		);

		if($dbc->Update("incomings",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$incoming = $dbc->GetRecord("incomings","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"incoming-approve",$_POST['id'],array("incomings" => $incoming));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
