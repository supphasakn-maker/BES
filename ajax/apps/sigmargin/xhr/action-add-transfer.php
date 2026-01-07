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
		'type' => $_POST['type'],
		'amount_usd' => $_POST['amount_usd'],
		'rate_pmdc' => $_POST['rate_pmdc'],
		'amount_total' => $_POST['amount_total'],
		'date' => $_POST['date']
	);

	if($dbc->Insert("bs_smg_payment",$data)){
		$transfer_id = $dbc->GetID();
		echo json_encode(array(
			'success'=>true,
			'msg'=> $transfer_id
		));

		$transfer = $dbc->GetRecord("bs_smg_payment","*","id=".$transfer_id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"transfer-add",$transfer_id,array("transfers" => $transfer));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Insert Error"
		));
	}

	$dbc->Close();
?>
