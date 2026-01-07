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
			'#id' => "DEFAULT",
			"type" => $_POST['type'],
			"#supplier_id" => $_POST['supplier_id'],
			"#amount" => $_POST['amount'],
			"#rate_spot" => $_POST['rate_spot'],
			"#rate_pmdc" => $_POST['rate_pmdc'],
			"date" => $_POST['date'],
			"value_date" => isset($_POST['value_date'])?$_POST['value_date']:$_POST['date'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"method" => $_POST['method'],
			"ref" => $_POST['ref'],
			"#user" => $os->auth['id'],
			"#status" => 1,
			"comment" => isset($_POST['comment'])?addslashes($_POST['comment']):"",
			"#trade_id" => 'NULL'
		);

		if($dbc->Insert("bs_sales_spot",$data)){
			$spot_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $spot_id
			));

			$spot = $dbc->GetRecord("bs_sales_spot","*","id=".$spot_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"spot-add",$spot_id,array("bs_sales_spot" => $spot));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
