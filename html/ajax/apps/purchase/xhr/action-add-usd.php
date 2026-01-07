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
			'msg'=>'Pleser input amount'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			"bank" => $_POST['bank'],
			"type" => $_POST['type'],
			"#amount" => $_POST['amount'],
			"#rate_exchange" => $_POST['rate_exchange'],
			"date" => $_POST['date'],
			"comment" => $_POST['comment'],
			"#user" => $os->auth['id'],
			"#status" => isset($_POST['pending'])?0:1,
			"#confirm" => isset($_POST['pending'])?'NULL':'NOW()',
			'#created' => 'NOW()',
			'#updated' => 'NOW()'
			
		);

		if($dbc->Insert("bs_purchase_usd ",$data)){
			$usd_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $usd_id
			));

			$usd = $dbc->GetRecord("bs_purchase_usd ","*","id=".$usd_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"usd-add",$usd_id,array("usd" => $usd));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
