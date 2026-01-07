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

	if($_POST['amount']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Amount should not empty!'
		));
	}else{
		
		$data = array(
			"#supplier_id" => $_POST['supplier_id'],
			"date" => $_POST['date'],
			"#amount" => $_POST['amount'],
			"#usd" => $_POST['usd']
		);

		if($dbc->Update("bs_adjust_purchase",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$spot = $dbc->GetRecord("bs_adjust_purchase","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"purchase-edit",$_POST['id'],array("bs_adjust_purchase" => $spot));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
