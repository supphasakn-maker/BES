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

	if($dbc->HasRecord("historys","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'History Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'#updated' => 'NOW()',
		);

		if($dbc->Update("historys",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$history = $dbc->GetRecord("historys","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"history-edit",$_POST['id'],array("historys" => $history));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
