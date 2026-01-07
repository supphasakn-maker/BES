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
	
	$info = json_decode($os->load_variable('aCompany_info'),true);
	
	/*
	
	$info = array(
		"org_name" => $_REQUEST['txtName'],
		"tax_id" => $_REQUEST['txtTax'],
		"address" => $_REQUEST['txtAddress'],
		"phone" => $_REQUEST['txtPhone'],
		"fax" => $_REQUEST['txtFax'],
		"email" => $_REQUEST['txtEmail'],
		"website" => $_REQUEST['txtWebsite'],
		"branch" => $_REQUEST['txtBranch']
	);
	
	
	if($dbc->HasRecord("variable","name='aCompany_info'")){
		$dbc->Update("variable",array(
				"name" => "aCompany_info",
				"value" => base64_encode(json_encode($info)),
				"#updated" => "NOW()"
		),"name='aCompany_info'");
		
	}else{
		$dbc->Insert("variable",array(
				"#id" => "DEFAULT",
				"name" => "aCompany_info",
				"value" => base64_encode(json_encode($info)),
				"#updated" => "NOW()"
		));
		
	}
	
	$variable = $dbc->GetRecord("variable","*","name='aCompany_info'");
	$os->save_log(0,$_SESSION['auth']['user_id'],"variable-edit",'aCompany_info',array("variable" => $variable));
	*/
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Passed"
	));
	
	$dbc->Close();
?>