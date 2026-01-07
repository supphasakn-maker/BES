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
		'date' => $_POST['date'],
		'amount' => $_POST['amount'],
		'rate_pmdc' => $_POST['rate_pmdc']
	);

	if($dbc->Update("bs_smg_receiving",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
		$incoming = $dbc->GetRecord("bs_smg_receiving","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"incoming-edit",$_POST['id'],array("bs_smg_receiving" => $incoming));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Change"
		));
	}


	$dbc->Close();
?>
