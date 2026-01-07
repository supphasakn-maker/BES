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


	if($dbc->HasRecord("bs_match_data ","date = '".$_POST['date']."'")){
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
			'#id' => "DEFAULT",
			'date' => $_POST['date'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'data' => json_encode($json_data)
		);

		if($dbc->Insert("bs_match_data ",$data)){
			$match_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $match_id
			));

			$match = $dbc->GetRecord("bs_match_data ","*","id=".$match_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"match-add",$match_id,array("bs_match_data " => $match));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
