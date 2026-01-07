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
	
	$unlink = "";
	$action = array();
	
	$iVariable = "iClaim";
	$path_begin = 'binary/claim/';
	
	
	
	
	if($_FILES['file']['name'][0]==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Photo Uploaded!"
		));
	}else{
		$fails=array();
		$uploads=array();
		$iNumber = $os->load_variable($iVariable);
		for($i=0;$i<count($_FILES['file']['name']);$i++){
			$iNumber++;
			$filename = $_FILES['file']['name'][$i];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$path = $path_begin.$iNumber.".".$ext;
			$file = array(
				"name" => $_FILES['file']['name'][$i],
				"type" => $_FILES['file']['type'][$i],
				"tmp_name" => $_FILES['file']['tmp_name'][$i],
				"error" => $_FILES['file']['error'][$i],
				"size" => $_FILES['file']['size'][$i]
			);
			$uploaded = $os->upload($file,"../../../".$path);
			if(!$uploaded['success']){
				array_push($fails,array(
					"filename" => $file['name'],
					"msg" => $uploaded['msg']
				));
			}else{
				array_push($uploads,$path);
			}
		}
		
		$os->save_variable($iVariable,$iNumber);
		echo json_encode(array(
			'success'=>true,
			'fail' => $fails,
			'uploaded' => $uploads
		));
	}

	$dbc->Close();
?>