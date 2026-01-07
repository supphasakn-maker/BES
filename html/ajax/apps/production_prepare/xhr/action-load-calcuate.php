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
	$sql = "SELECT * FROM bs_packing_items WHERE production_id = ".$_POST['id'];
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		
		$iterator = -1;
		for($i=0;$i<count($data);$i++){
			if($data[$i]['name']==$item['pack_name']){
				$iterator = $i;
			}
		}
		
		if($iterator>-1){
			$data[$iterator]['total']++;
			$data[$iterator]['weight'] += $item['weight_actual'];
			$data[$iterator]['remark'] .= ",".$item['code'];
			
		}else{
			array_push($data,array(
				"name" => $item['pack_name'],
				"total" => 1,
				"weight" => $item['weight_actual'],
				"remark" => $item['code']
			));
		}
		
		
		
	}
	
		
	echo json_encode(array(
		'success'=>true,
		'data'=> $data
	));

	

	$dbc->Close();
?>
