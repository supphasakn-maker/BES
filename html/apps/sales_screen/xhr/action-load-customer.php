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
	$customer = $dbc->GetRecord("bs_customers","*","id=".$_POST['id']);
	if($customer['default_sales']!=""){
		$employee = $dbc->GetRecord("bs_employees","*","id=".$customer['default_sales']);
		$group = $dbc->GetRecord("bs_customer_groups","*","id=".$customer['gid']);
		$customer['default_sales'] = $employee['fullname'];
		$customer['product_id'] = $group['product_id'];
	}
	$extern = json_encode($customer,JSON_UNESCAPED_UNICODE);
	echo $extern;
	$dbc->Close();
?>