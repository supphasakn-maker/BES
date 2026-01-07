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
			"#product_id" => $_POST['product_id'],
			"remark" => $_POST['remark'],
			"#amount" => $_POST['amount'],
			'#updated' => 'NOW()',
            "date" => $_POST['date'],
            "#type_id" => $_POST['type_id'],
            "#product_type" => $_POST['product_type']
			
		);

		if($dbc->Update("bs_stock_adjusted_bwd",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$adjust = $dbc->GetRecord("bs_stock_adjusted_bwd","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"adjust-bwd-edit",$_POST['id'],array("adjusts" => $adjust));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
