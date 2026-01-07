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
        bs_stock_bwd.id AS id,
        bs_stock_bwd.code AS code
		FROM bs_stock_bwd 
		LEFT JOIN bs_bwd_pack_items ON bs_bwd_pack_items.item_id = bs_stock_bwd.id
		WHERE bs_stock_bwd.code LIKE '%".$_GET['term']."%'
		AND bs_bwd_pack_items.id IS NULL
	";
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
