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


	if($dbc->HasRecord("prepares","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Prepare Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()'
		);

		if($dbc->Insert("prepares",$data)){
			$prepare_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $prepare_id
			));

			$prepare = $dbc->GetRecord("prepares","*","id=".$prepare_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"prepare-add",$prepare_id,array("prepares" => $prepare));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
