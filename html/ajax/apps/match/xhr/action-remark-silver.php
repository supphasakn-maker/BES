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
			'remark' => addslashes($_POST['remark']),
		);

		if($dbc->Update("bs_mapping_silvers",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$silver = $dbc->GetRecord("bs_mapping_silvers","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"silver-remark",$_POST['id'],array("silvers" => $silver));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
