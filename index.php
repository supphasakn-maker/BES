<?php
	session_start();
	@ini_set('display_errors',1);
	include_once "config/define.php";
	include_once "include/db.php";
	include_once "include/oceanos.php";
	include_once "include/nebulaos.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new nebulaos($dbc);
	$abox = $os;
	
	
	if(is_null($os->auth)){
		include_once "iface/login.php";
	}else{
		include_once "iface/bootstrap.php";
	}
	
	//include_once "iface/bootstrap.php";
	
	
	
	/*
	$sql = "SHOW TABLES LIKE 'variable'";
	$rst = $dbc->Query($sql);
	
	
	if($dbc->Total($rst)>0){
		
		if(is_null($abox->auth)){
			include_once "iface/login.php";
		}else{
			include_once "iface/bootstrap.php";
		}
	}else{
		
		include_once "iface/install.php";
		
	}
	*/

	$dbc->Close();
	
?>