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
	
	$delivery_id = $_POST['id'];
	$delivery = $dbc->GetRecord("bs_deliveries","*","id=".$delivery_id);
	$order = $dbc->GetRecord("bs_orders","*","delivery_id=".$delivery_id);
	
	$total = 0;
	$sql = "SELECT
		bs_packing_items.weight_actual as weight_actual
	FROM bs_packing_items 
	LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id= bs_delivery_pack_items.item_id
	WHERE bs_delivery_pack_items.delivery_id = ".$delivery_id;
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		$total += $item['weight_actual'];
	}
	
	$remain = $order['amount'] - $total;
	echo json_encode(array(
		"order"=>$data,
		"delivery"=>$delivery,
		"total"=>number_format($total,4),
		"remain"=>number_format($remain,4)
	
	));

	$dbc->Close();
?>
