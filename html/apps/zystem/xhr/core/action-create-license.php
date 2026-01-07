<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);


	if($dbc->HasRecord("servers","name = '".$_POST['machine_id']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Server Name is already exist.'
		));
	}else{
		$data = array(
			"brand" => $_POST['brand'],
			'machine_type' => $_POST['machine_type'],
			'machine_id' => $_POST['machine_id'],
			"model" => $_POST['model'],
			"created" => time()
		);
		
		$code = base64_encode(base64_encode(json_encode($data)));
		
		try {
			$file = fopen("../../../../binary/tmp/server.key", "w") or die("Unable to open file!");
			fwrite($file, $code);
			fclose($file);
			echo json_encode(array(
				'success'=>true,
				'link'=> "binary/tmp/server.key"
			));
			//$os->save_log(0,$_SESSION['auth']['user_id'],"server-add",0,array("servers" => $data));
		} catch (Exception $e) {
			echo json_encode(array(
				'success'=>false,
				'msg' => 'Caught exception: ',  $e->getMessage(), "\n"
			));
		}
	}

	$dbc->Close();
?>
