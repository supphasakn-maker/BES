<?php
	session_start();
	include_once "../config/define.php";
	include_once "../include/db.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$user = $dbc->GetRecord("os_users","*","id=".$_SESSION['auth']['user_id']);
	$setting = json_decode($user['setting'],true);
	
	$setting['lang'] = $_POST['lang'];
	
	$dbc->Update("os_users",array(
		"setting" => json_encode($setting)
	),"id=".$_SESSION['auth']['user_id']);
	$dbc->Close();
?>