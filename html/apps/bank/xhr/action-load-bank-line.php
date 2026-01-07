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

	
	$data = array();
	$sql = "SELECT * 
	FROM bs_bank_statement 
	WHERE bank_id = ".$_POST['bank_id']." 
		AND  date = '".$_POST['bank_date']."'
		AND type = 2
	";
	$rst = $dbc->Query($sql);
	while($statement = $dbc->Fetch($rst)){
		if(!$dbc->HasRecord("bs_bank_statement","transfer_to =".$statement['id']))
			array_push($data,$statement);
	}
	
	echo json_encode($data);
	
?>