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
		'weight_expected' => $_POST['weight_expected'],
		'#updated' => 'NOW()'
	);

	if($dbc->Update("bs_scrap_items",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
		$scrap = $dbc->GetRecord("bs_scrap_items","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"scrap-edit",$_POST['id'],array("scrap" => $scrap));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Change"
		));
	}
	

	$dbc->Close();
?>
