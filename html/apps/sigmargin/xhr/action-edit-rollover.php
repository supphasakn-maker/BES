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

	$data = array(
		'trade' => $_POST['trade'],
		'date' => $_POST['date'],
		'type' => $_POST['type'],
		'amount' => $_POST['amount'],
		'rate_spot' => $_POST['rate_spot'],
		'entry' => $_POST['entry'],
		
	);

	if($dbc->Update("bs_smg_rollover",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
		$rollover = $dbc->GetRecord("bs_smg_rollover","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"rollover-edit",$_POST['id'],array("bs_smg_rollover" => $rollover));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Change"
		));
	}
	

	$dbc->Close();
?>
