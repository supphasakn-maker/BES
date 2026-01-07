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

	$dt = date($_POST['date']);
	if(date("l")=="Thursday"){
		$date2 = date("Y-m-d", strtotime("+4 day",strtotime($dt)));
	}else if(date("l")=="Friday"){
		$date2 = date("Y-m-d", strtotime("+4 day",strtotime($dt)));
	}else{
		$date2 = date("Y-m-d", strtotime("+2 day",strtotime($dt)));
	}
	


	if($_POST['amount']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Amount should not empty!'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			"#supplier_id" => $_POST['supplier_id'],
			"type" => $_POST['type'],
			"#amount" => $_POST['amount'],
			"#rate_spot" => $_POST['rate_spot']!=""?$_POST['rate_spot']:"NULL",
			"#rate_pmdc" => $_POST['rate_pmdc']!=""?$_POST['rate_pmdc']:"NULL",
			"#THBValue" => $_POST['THBValue']!=""?$_POST['THBValue']:"NULL",
			"currency" => $_POST['currency'],
			"date" => $_POST['date'],
			"value_date" => isset($_POST['value_date'])?$_POST['value_date']:$_POST['date'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"method" => $_POST['method'],
			"ref" => $_POST['ref'],
			"#user" => $os->auth['id'],
			"#status" => isset($_POST['pending'])?0:1,
			"#confirm" => isset($_POST['pending'])?'NULL':'NOW()',
			"#order_id" => 'NULL',
			"comment" => isset($_POST['comment'])?$_POST['comment']:"",
			"#adj_supplier	" => $_POST['adj_supplier'],
			"#product_id	" => $_POST['product_id'],
            "#claim	" => 1
		);
		$datasmg = array(
			'#id' => "DEFAULT",
			"date" => $date2,
			"type" => $_POST['type'],
			"purchase_type"  => 'Buy',
			"#amount" => $_POST['amount'],
			"#rate_spot" => $_POST['rate_spot']!=""?$_POST['rate_spot']:"NULL"
		);

		if($dbc->Insert("bs_purchase_spot",$data)){
			$spot_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $spot_id
			));
			$dbc->Insert("bs_smg_trade",$datasmg);
			$spot = $dbc->GetRecord("bs_purchase_spot","*","id=".$spot_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"spot-add",$spot_id,array("spot" => $spot));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
