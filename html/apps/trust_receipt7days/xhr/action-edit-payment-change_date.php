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

	if($_POST['date'] == ''){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Tr Code is already exist.'
		));
	}else{
		$data = array(
			'date' => $_POST['date']
		);

		if($dbc->Update("bs_transfer_payments",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$tr = $dbc->GetRecord("bs_transfer_payments","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"tr-edit",$_POST['id'],array("trs" => $tr));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
