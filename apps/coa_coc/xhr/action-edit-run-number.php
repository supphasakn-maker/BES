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
			"number" => $_POST['number'],
			"number_coc" => $_POST['number_coc'],
			"#customer_id" => $_POST['customer_id'],
			"order_id" => $_POST['order_code'],
			"created" => $_POST['delivery_date'],
			"#status" => 2
		);

		if($dbc->Update("bs_coa_run",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$coa_coc = $dbc->GetRecord("bs_coa_run","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"coa-coc-number-run",$_POST['id'],array("coa-coc" => $coa_coc));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
