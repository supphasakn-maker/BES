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

	if($_POST['change_buy']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input change_buy!'
		));
	}else{
		$change_buy = $os->save_variable("change_buy",$_POST['change_buy']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"change-change_buy",$_POST['change_buy'],array(
			'exchange' => $_POST['change_buy'],
			'date' => date('Y-m-d H:i:s')
		));
		echo json_encode(array('success'=>true));
	}

	$dbc->Close();
?>
