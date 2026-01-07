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
	
	if($dbc->HasRecord("variable","name='bMenu'")){
		$dbc->Update("variable",array(
			"value" => $_POST['available'],
			"#updated" => "NOW()",
		),"name='bMenu'");
	}else{
		$dbc->Insert("variable",array(
			"#id" => "DEFAULT",
			"name" => "bMenu",
			"value" => $_POST['available'],
			"#updated" => "NOW()"
		));
	}
	
	
	if($dbc->HasRecord("variable","name='aMenu'")){
		$dbc->Update("variable",array(
			"value" => $_POST['data'],
			"#updated" => "NOW()",
		),"name='aMenu'");
	}else{
		$dbc->Insert("variable",array(
			"#id" => "DEFAULT",
			"name" => "aMenu",
			"value" => $_POST['data'],
			"#updated" => "NOW()"
		));
	}
	$variable = $dbc->GetRecord("variable","*","name='aMenu'");
	$variable2 = $dbc->GetRecord("variable","*","name='bMenu'");
	$os->save_log(0,$_SESSION['auth']['user_id'],"variable-edit",$item[0],array("variable" => $variable,"variable2" => $variable2));
	
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Passed"
	));
	
	$dbc->Close();
?>