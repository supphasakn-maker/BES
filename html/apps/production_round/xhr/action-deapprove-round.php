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
			'status' => 1,
            '#amount_balance' => $_POST['amount']
		);

		if($dbc->Update("bs_productions_round",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$payment = $dbc->GetRecord("bs_productions_round","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"round-dapprove",$_POST['id'],array("round" => $payment));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
