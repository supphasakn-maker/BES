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
	
	$sql = "SELECT
			bs_delivery_items.name AS name,
			bs_delivery_items.size AS size,
			SUM(bs_delivery_items.amount) AS amount,
			bs_delivery_items.comment AS comment
		FROM bs_delivery_items
		LEFT JOIN bs_deliveries ON bs_delivery_items.delivery_id = bs_deliveries.id
		WHERE bs_deliveries.delivery_date BETWEEN  '$_POST[date_from]' AND '$_POST[date_to]'
		GROUP BY bs_delivery_items.name,bs_delivery_items.size,bs_delivery_items.comment
		ORDER BY bs_delivery_items.name DESC 
	";
	
	
	$rst = $dbc->Query($sql);
	while($line=$dbc->Fetch($rst)){
		array_push($data,$line);
	}
	
	
	echo json_encode($data);
	

	$dbc->Close();
?>
