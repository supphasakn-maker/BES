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
		"date" => $_POST['date'],
		"type" => $_POST['type'],
		"purchase_type" => $_POST['purchase_type'],
		"#amount" => $_POST['amount'],
		"#rate_spot" => $_POST['rate_spot'],
		"#rate_pmdc" => $_POST['rate_pmdc']!=""?$_POST['rate_pmdc']:0
	);

	if($dbc->Insert("bs_smg_trade",$data)){
		$silver_id = $dbc->GetID();
		echo json_encode(array(
			'success'=>true,
			'msg'=> $silver_id
		));

		$silver = $dbc->GetRecord("bs_smg_trade","*","id=".$silver_id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"silver-add",$silver_id,array("bs_smg_trade" => $silver));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Insert Error"
		));
	}

	$dbc->Close();
?>
