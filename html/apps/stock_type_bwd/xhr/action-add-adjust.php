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

	if($_POST['product_id']=="1"){		
        $weight = "0.015";
	}else if ($_POST['product_id']=="2"){      
        $weight = "0.050";
	}else{      
        $weight = "0.150";
	}

	if($_POST['product_id']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Product should not empty!'
		));
	}else if($_POST['amount']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'กรุณากรอกจำนวนแท่ง!'
		));
	}else if($_POST['type_id']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Type should not empty!'
		));
	}else if($_POST['product_type']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Product Type should not empty!'
		));
	}else{
	
		$data = array(
			'#id' => "DEFAULT",
            "#product_id" => $_POST['product_id'],
            "remark" => $_POST['remark'],
            "#weight_expected" => $weight,
            "#amount" => $_POST['amount'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
            "date" => $_POST['date'],
			"#type_id" => $_POST['type_id'],
			"#product_type" => $_POST['product_type']
		);
	

		if($dbc->Insert("bs_stock_adjusted_bwd",$data)){
			$adjust_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $adjust_id
			));

			$adjust = $dbc->GetRecord("bs_stock_adjusted_bwd","*","id=".$adjust_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"adjust-bwd-add",$adjust_id,array("adjusts" => $adjust));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	
	}
	$dbc->Close();
?>
