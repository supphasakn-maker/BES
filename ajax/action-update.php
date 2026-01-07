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
	$data = array(
		"#connected" => "NOW()"
	);
	
	$dbc->Update("concurrents",$data,"session_id LIKE '".$session_id."'");
	$result = array(
		"success" => true,
		"server_time" => time(),
		"process" => array()
	);
	
	$sql = "
		SELECT 
			messages.id AS id,
			messages.msg AS msg,
			messages.created AS created,
			messages.source AS source,
			users.name AS sender
		FROM messages 
		LEFT JOIN users ON messages.source = users.id
		WHERE
			messages.destination = $user_id 
		AND 
			messages.opened IS NULL
		ORDER BY messages.id DESC";
	$rst = $dbc->Query($sql);
	while($msg = $dbc->Fetch($rst)){
		$process = array(
			"action" => "instance_message",
			"msg" => $msg['msg'],
			"time" => $msg['created'],
			"source" => $msg['source'],
			"sender" => $msg['sender']
		);
		array_push($result['process'],$process);
		
		$dbc->Update("messages",array("#opened"=>"NOW()"),"id=".$msg['id']);
		
	}
	
	echo json_encode($result);
	$dbc->Close();
?>

