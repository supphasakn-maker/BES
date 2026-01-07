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
	
	
	foreach($_POST as $key => $val){
		if($dbc->HasRecord("variable","name='". $key."'")){
			$dbc->Update("variable",array(
				"name" =>  $key,
				"value" => $val,
				"#updated" => "NOW()"
			),"name='". $key."'");
		}else{
			$dbc->Insert("variable",array(
				"#id" => "DEFAULT",
				"name" =>  $key,
				"value" => $val,
				"#updated" => "NOW()"
			));
		}
		$variable = $dbc->GetRecord("variable","*","name='". $key."'");
		$os->save_log(0,$_SESSION['auth']['user_id'],"variable-edit", $key,array("variable" => $variable));
	}
	
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Passed"
	));
	
	$dbc->Close();
?>