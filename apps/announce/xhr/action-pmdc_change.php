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

	if($_POST['pmdc_rate']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input rate pmdc!'
		));
	}else{
		$pmdc_rate = $os->save_variable("pmdc_rate",$_POST['pmdc_rate']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"change-rate-pmdc_rate",$_POST['pmdc_rate'],array(
			'exchange' => $_POST['pmdc_rate'],
			'date' => date('Y-m-d H:i:s')
		));
		echo json_encode(array('success'=>true));
	}

	$dbc->Close();
?>
