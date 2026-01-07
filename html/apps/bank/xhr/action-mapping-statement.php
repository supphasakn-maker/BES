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

	if($_POST['bank_line']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'No Selected.'
		));
	}else{
		$data = array(
			'#transfer_to' => $_POST['bank_line']
		);

		if($dbc->Update("bs_bank_statement",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$statement = $dbc->GetRecord("bs_bank_statement","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"statement-mapping",$_POST['id'],array("statements" => $statement));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
