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
	
	$os->set_upload_allow('gif,png,jpg,svg,pdf,jpge,xls,doc,xlsx,docx');

	$action = array();
	
	$iVariable = "iImport";
	$path_begin = 'binary/import/';
	
	$aError = array();
	$aPath = array();
	
	
	if(!isset($_FILES['file'])){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please upload photo"
		));
	}else{
		for($i=0;$i<count($_FILES['file']['name']);$i++){
			$iNumber = $os->load_variable($iVariable);
			$iNumber++;
			$filename = $_FILES['file']['name'][$i];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$path = $path_begin.$iNumber.".".$ext;
			$file = array(
				"name" => $_FILES['file']['name'][$i],
				"tmp_name" => $_FILES['file']['tmp_name'][$i],
				"error" => $_FILES['file']['error'][$i]
			);
			try{
				$uploaded = $os->upload($file,"../../../".$path);
				
				if($uploaded['success']){
					$os->save_variable($iVariable,$iNumber);
					array_push($aPath,$path);
				}else{
					array_push($aError,$uploaded['msg']);
				}
			} catch (Exception $e) {
				array_push($aError,$e);
			}
		}
		
		echo json_encode(array(
			'success'=>true,
			'path' => $aPath,
			'error' => $aError
		));
	}

	$dbc->Close();
?>