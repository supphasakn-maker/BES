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


	if($dbc->HasRecord("historys","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'History Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()'
		);

		if($dbc->Insert("historys",$data)){
			$history_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $history_id
			));

			$history = $dbc->GetRecord("historys","*","id=".$history_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"history-add",$history_id,array("historys" => $history));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
