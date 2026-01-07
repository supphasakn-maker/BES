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
	
	$sql = "SELECT * FROM bs_purchase_spot
	
		WHERE supplier_id =".$_POST['supplier_id']." 
		AND status > -1 and type LIKE 'defer' and transfer_id IS NULL";
	$rst = $dbc->Query($sql);
	while($purchase = $dbc->Fetch($rst)){
		$supplier = $dbc->GetRecord("bs_suppliers","name","id=".$purchase['supplier_id']);
		array_push($data,array(
			'id' => $purchase['id'],
			'date' => $purchase['date'],
			'amount' => $purchase['amount'],
			'supplier' => $supplier['name'],
			'usd' => $purchase['amount']*($purchase['rate_pmdc']+$purchase['rate_spot'])*32.1507
			
		));
	}
	
	echo json_encode($data);

	$dbc->Close();
?>