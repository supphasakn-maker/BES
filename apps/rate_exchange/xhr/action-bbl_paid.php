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


		$bbl_paid = $os->save_variable("bbl_paid",$_POST['bbl_paid']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"change-rate-bbl_paid",$_POST['bbl_paid'],array(
			'exchange' => $_POST['bbl_paid'],
			'date' => date('Y-m-d H:i:s')
		));
		echo json_encode(array('success'=>true));
	

	$dbc->Close();
?>
