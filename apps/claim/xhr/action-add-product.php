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
			'#id' => "DEFAULT",
			"type" => $_POST['type'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"date_claim" => $_POST['date_claim'],
			"#order_id" => $_POST['order_id'],
			"issue" => $_POST['issue'],
			"#amount" => $_POST['amount']!=""?$_POST['amount']:"NULL",
			"pack_problem" => $_POST['pack_problem'],
			"pack_claim" => $_POST['pack_claim'],
			"detail" => $_POST['detail'],
			"#status" => 0,
			"#submitted" => 'NULL',
			"#approved" => 'NULL',
			"#approver_id" => 'NULL',
			"#rejected" => 'NULL',
			"#solved" => 'NULL',
			"#solver_id" => 'NULL',
			"#closed" => 'NULL',
			"#user_id" => $os->auth['id'],
			"org_name" => $_POST['org_name'],
			"contact_issuer" => $_POST['contact_issuer'],
			"contact_sender" => $_POST['contact_sender'],
			"contact_sales" => $_POST['contact_sales'],
			"#product_id" =>  $_POST['product_id'],

		);


		if($dbc->Insert("bs_claims",$data)){
			$claim_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $claim_id
			));

			$code = "CLAIM-".sprintf("%07s", $claim_id);
			$dbc->Update("bs_claims",array("code"=>$code),"id=".$claim_id);

			$claim = $dbc->GetRecord("bs_claims","*","id=".$claim_id);


			$os->save_log(0,$_SESSION['auth']['user_id'],"claim-add",$claim_id,array("claim" => $claim));

	
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	

	$dbc->Close();
?>
