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
	
	$pmr_id = $_POST['id'];
	$pmr = $dbc->GetRecord("bs_productions_pmr","*","id=".$pmr_id);
	
	$total = 0;
	$sql = "SELECT
		bs_packing_items.weight_actual as weight_actual
	FROM bs_packing_items 
	LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
	WHERE bs_pmr_pack_items.pmr_id = ".$pmr_id;
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		$total += $item['weight_actual'];
	}
	
	$remain = $pmr['weight_out_total'] - $total;
	echo json_encode(array(
		"order"=>$data,
		"delivery"=>$pmr,
		"total"=>number_format($total,4),
		"remain"=>number_format($remain,4)
	
	));

	$dbc->Close();
?>
