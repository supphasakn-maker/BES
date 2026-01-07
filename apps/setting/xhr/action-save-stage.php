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
	
	for($i=0;$i<count($_POST['txtID']);$i++){
		if($_POST['txtID'][$i] == ""){
			$setting = array(
				"created" => time(),
				"updated" => time()
			);
			$data = array(
				"#id" => "DEFAULT",
				"name" => $_POST['txtName'][$i],
				"detail" => $_POST['txtName'][$i],
				"color" => $_POST['txtColor'][$i],
				"#icon" => "NULL",
				"#priority" => $i,
				"setting" => json_encode($setting),
				"#status" => $_POST['cbbStatus'][$i]
			);
			$dbc->Insert("sales_stages",$data);
		}else{
			if($_POST['flagRemove'][$i]=="yes"){
				$dbc->Delete("sales_stages","id=".$_POST['txtID'][$i]);
			}else{
				$sales = $dbc->GetRecord("sales_stages","*","id=".$_POST['txtID'][$i]);
				$setting = json_decode($sales['setting'],true);
				$setting['updated'] = time();
				$data = array(
					"name" => $_POST['txtName'][$i],
					"detail" => $_POST['txtName'][$i],
					"color" => $_POST['txtColor'][$i],
					"#priority" => $i,
					"setting" => json_encode($setting),
					"#status" => $_POST['cbbStatus'][$i]
				);
				$dbc->Update("sales_stages",$data,"id=".$_POST['txtID'][$i]);
			}
		}
	}
	
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Passed"
	));
	
	$dbc->Close();
?>