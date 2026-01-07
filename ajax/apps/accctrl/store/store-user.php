<?php
	session_start();
	ini_set('display_errors',1);
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";
	
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new datastore;
	$dbc->Connect();
	
	$columns = array(
		"id" => "os_users.id",
		"username" => "os_users.name",
		"fullname" => "CONCAT(os_contacts.name,' ',os_contacts.surname)",
		"phone" => "os_contacts.phone",
		"mobile" => "os_contacts.mobile",
		"groupname" => "os_groups.name",
		"status" => "os_users.status",
		"last_login" => "IF(os_users.last_login IS NULL,'-',os_users.last_login)",
		"avatar" => "os_contacts.avatar",
		"email" => "os_contacts.email",
		"contact_id" => "os_contacts.id"
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_users",
		"join" => array(
			array(
				"field" => "gid",
				"table" => "os_groups",
				"with" => "id"
			),
			array(
				"field" => "contact",
				"table" => "os_contacts",
				"with" => "id"
			)
		)
	);
	
	if(isset($_GET['account']) && $_GET['account']!=""){
		$table["where"] = "os_groups.account=".$_GET['account'];
	}
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>



