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
	
	foreach($_POST['import'] as $import_id){
		$dbc->Update("bs_imports",array("#transfer_id" => $_POST['id']),"id=".$import_id);
	}


	echo json_encode(array(
		'success'=>true
	));

	

	
	

	$dbc->Close();
?>
