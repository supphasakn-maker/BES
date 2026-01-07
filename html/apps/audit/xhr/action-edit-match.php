<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../include/const.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	if($dbc->HasRecord("bs_match_data","date = '".$_POST['date']."' AND id !=".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Match Name is already exist.'
		));
	}else{
		$json_data = array();
		foreach($aData as $field){
			$json_data[$field[0]] = $_POST[$field[0]];
		}

		$data = array(
			'date' => $_POST['date'],
			'#updated' => 'NOW()',
			'data' => json_encode($json_data)
		);

		if($dbc->Update("bs_match_data",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$match = $dbc->GetRecord("bs_match_data","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"match-edit",$_POST['id'],array("bs_match_data" => $match));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
