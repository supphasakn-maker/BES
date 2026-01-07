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

	if($_POST['rate']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input rate number!'
		));
	}else{
		$rate_exchange = $os->save_variable("rate_exchange",$_POST['rate']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"change-rate-exchange",$_POST['rate'],array(
			'exchange' => $_POST['rate'],
			'date' => date('Y-m-d H:i:s')
		));
		echo json_encode(array('success'=>true));
	}

	$dbc->Close();
?>
