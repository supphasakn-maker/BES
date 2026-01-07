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


	if($dbc->HasRecord("transactions","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Transaction Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()'
		);

		if($dbc->Insert("transactions",$data)){
			$transaction_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $transaction_id
			));

			$transaction = $dbc->GetRecord("transactions","*","id=".$transaction_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"transaction-add",$transaction_id,array("transactions" => $transaction));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
