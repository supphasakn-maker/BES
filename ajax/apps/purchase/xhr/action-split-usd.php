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
	
	
	if($_POST['amount'] != $_POST['split']+$_POST['remain']){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'The balance is not correct'
		));
	}else{
		$usd = $dbc->GetRecord("bs_purchase_usd","*","id=".$_POST['id']);
		
		$data = array(
			'#id' => "DEFAULT",
			"bank" => $usd['bank'],
			"type" => $usd['type'],
			"#amount" => $_POST['split'],
			"#rate_exchange" => $usd['rate_exchange'],
			"date" => $usd['date'],
			"comment" => $usd['comment'],
			"ref" => $usd['ref'],
			"#user" => $os->auth['id'],
			"#status" => $usd['status'],
			"confirm" => $usd['confirm'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#parent" => $usd['id']
		);
		
		
		$dbc->Insert("bs_purchase_usd",$data);
		$data['#amount'] = $_POST['remain'];
		$dbc->Insert("bs_purchase_usd",$data);
		
		$data = array(
			"#status" => -1,
			'#updated' => 'NOW()',
		);

		if($dbc->Update("bs_purchase_usd",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$usd = $dbc->GetRecord("bs_purchase_usd","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"usd-split",$_POST['id'],array("usds" => $usd));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
