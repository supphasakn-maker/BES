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
	if($_POST['production_id']!=""){
		$sql = "SELECT * FROM bs_packing_items";
		$sql .= " WHERE production_id = ".$_POST['production_id'];
		
		$rst = $dbc->Query($sql);
		while($item = $dbc->Fetch($rst)){
			if(!$dbc->HasRecord("bs_pmr_pack_items","item_id=".$item['id']))
			array_push($data,array(
				"id" => $item['id'],
				"text" => $item['code']
			));
		}
	}
	
	echo json_encode($data);

	$dbc->Close();
?>