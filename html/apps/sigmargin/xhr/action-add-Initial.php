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
		'date_start' => $_POST['date_start'],
		'date_end' => $_POST['date_end'],
		'margin' => $_POST['margin']
	);

	if($dbc->Insert("bs_smg_initial",$data)){
		$Initial_id = $dbc->GetID();
		echo json_encode(array(
			'success'=>true,
			'msg'=> $Initial_id
		));

		$Initial = $dbc->GetRecord("bs_smg_initial","*","id=".$Initial_id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"Initial-add",$Initial_id,array("bs_smg_initial" => $Initial));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Insert Error"
		));
	}
	

	$dbc->Close();
?>
