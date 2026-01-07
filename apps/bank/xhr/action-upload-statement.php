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
	
	if($_FILES['file']['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please upload file"
		));
	}else{
		$table = array();
		
		
		$filename = $_FILES['file']['tmp_name'];
		$filepath = $_FILES['file']['tmp_name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		
		
		$row = 1;
		if (($handle = fopen($filepath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 3000, ",")) !== FALSE) {
				$num = count($data);
				$row++;
				$line = array();
				for ($c=0; $c < $num; $c++) {
					array_push($line,$data[$c]);
					
				}
				array_push($table,$line);
			}
			fclose($handle);
		}
		echo json_encode(array(
			'success'=>true,
			'table' => $table,
		));
	}
	

	
	$dbc->Close();
?>