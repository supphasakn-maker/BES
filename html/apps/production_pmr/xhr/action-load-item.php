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
	
	$sql = "
		SELECT 
			bs_packing_items.id AS id,
			bs_packing_items.code AS code
		FROM bs_packing_items 
		LEFT JOIN bs_pmr_pack_items ON bs_pmr_pack_items.item_id = bs_packing_items.id
		WHERE bs_packing_items.code LIKE '%".$_GET['term']."%'
		AND bs_pmr_pack_items.id IS NULL
	";
	if($_GET['production_id']!=""){
		$sql .= " AND bs_packing_items.production_id = ".$_GET['production_id'];
	}
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		array_push($data,array(
			"id" => $item['id'],
			"text" => $item['code']
		));
	}
	
	
	echo json_encode(array(
	"results"=>$data));

	$dbc->Close();
?>
