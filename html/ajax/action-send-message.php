<?php
	session_start();
	@ini_set('display_errors',1);
	include_once "../../config/define.php";
	include_once "../../include/db.php";
	include_once "../../include/concurrent.php";
	
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$session_id = session_id();
	$user_id = $_SESSION['auth']['user_id'];
	$dest_id = $_POST['to'];
	$message = $_POST['msg'];
	
	$data = array(
		"#id" => "DEFAULT",
		"#source" => $user_id,
		"#destination" => $dest_id,
		"#type" => 1,
		"msg" => addslashes($message),
		"#created" => "NOW()",
		"#updated" => "NOW()",
		"#opened" => "NULL",
		"#acknowledge" => "NULL"
	);
	$dbc->Insert("messages",$data);
	
	$result = array(
		"success" => true,
		"server_time" => time()
	);
	
	echo json_encode($result);
	
	$dbc->Close();
?>

