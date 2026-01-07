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
	$order = $dbc->GetRecord("bs_orders_bwd","*","delivery_id=".$delivery_id);
	
	$total = 0;
	$sql = "SELECT
		bs_stock_bwd.amount as amount
	FROM bs_stock_bwd 
	LEFT JOIN bs_bwd_pack_items ON bs_stock_bwd.id= bs_bwd_pack_items.item_id
	WHERE bs_bwd_pack_items.delivery_id = ".$delivery_id;
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		$total += $item['amount'];
	}
	
	$remain = $order['amount'] - $total;
	echo json_encode(array(
		"order"=>$data,
		"delivery"=>$order,
		"total"=>number_format($total,4),
		"remain"=>number_format($remain,4)
	
	));

	$dbc->Close();
?>
