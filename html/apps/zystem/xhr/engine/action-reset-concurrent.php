<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	include_once "../../include/engine.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	function RandomString(){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 32; $i++) {
			//$randstring .= $characters[rand(0, strlen($characters))];
			$randstring .= substr($characters,rand(0, strlen($characters)),1);
			
		}
		return $randstring;
	}
	
	if($_POST['concurrent']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert concurrent number'
		));
	}else{
		
		$tablename = 'os_concurrents';
		$sql = "TRUNCATE ".$tablename;
		$dbc->Query($sql);
			
		for($i=0;$i<$_POST['concurrent'];$i++){
			$token = RandomString();
			$sql = "INSERT INTO ".$tablename."(id,token,package,created,updated,status,session_id,user_id,user_name,device,login,connected)";
			$sql .= "VALUES(DEFAULT,'$token',1,NOW(),NOW(),0,NULL,NULL,NULL,NULL,NULL,NULL)";
			$dbc->Query($sql);
		}
		
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> "Passed"
		));
	}
	
	$dbc->Close();
?>