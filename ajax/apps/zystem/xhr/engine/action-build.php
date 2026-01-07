<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	include_once "../../include/engine.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	if($_POST['name'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert name'
		));
	}else if($_POST['appname'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert appname'
		));
	}else{
		try {
			$engine = new engine($_POST);
			$engine->create_directory();
			$engine->write_index();
			$engine->write_include();
			$engine->write_control();
			$engine->write_store();
			$engine->write_view();
			$engine->write_xhr();
			$path = $engine->create_zip();
			
			echo json_encode(array(
				'success'=>true,
				'msg'=> "Passed",
				'path' => $path
			));
			
		} catch (Exception $e) {
			echo json_encode(array(
				'success'=>false,
				'msg'=> 'Caught exception: ',  $e->getMessage(), "\n"
			));
		}
	}
	
	$dbc->Close();
?>