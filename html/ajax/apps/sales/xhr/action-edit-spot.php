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
			'msg'=>'Please input amount'
		));
	}else{
		$data = array(
			"type" => $_POST['type'],
			"#supplier_id" => $_POST['supplier_id'],
			"#amount" => $_POST['amount'],
			"#rate_spot" => $_POST['rate_spot'],
			"#rate_pmdc" => $_POST['rate_pmdc'],
			"date" => $_POST['date'],
			"value_date" => $_POST['value_date'],
			'#updated' => 'NOW()',
			"method" => $_POST['method'],
			"ref" => $_POST['ref'],
			"comment" => $_POST['comment']
		);

		if($dbc->Update("bs_sales_spot",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$spot = $dbc->GetRecord("bs_sales_spot","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"spot-edit",$_POST['id'],array("bs_sales_spot" => $spot));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
