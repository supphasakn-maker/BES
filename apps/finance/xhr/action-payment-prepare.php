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

	if($dbc->HasRecord("prepares","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Prepare Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'#updated' => 'NOW()',
		);

		if($dbc->Update("prepares",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$prepare = $dbc->GetRecord("prepares","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"prepare-payment",$_POST['id'],array("prepares" => $prepare));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
