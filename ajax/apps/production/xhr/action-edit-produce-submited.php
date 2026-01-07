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

	if($_POST['submited']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'No Items'
		));
	}else{
		$data = array(
			'submited' => $_POST['submited']
		);
		
		

		if($dbc->Update("bs_productions",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			
			$produce = $dbc->GetRecord("bs_productions","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"produce-edit-submited",$_POST['id'],array("bs_productions" => $produce));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
