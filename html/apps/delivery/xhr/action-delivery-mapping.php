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

	if(!$dbc->HasRecord("bs_packing_items","id = ".$_POST['packing_id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Not FOund.'
		));
	}else{
		$data = array(
			"#id" => "DEFAULT",
			"#delivery_id" => $_POST['delivery_id'],
			"#item_type" => 1,
			"#item_id" => $_POST['packing_id'],
			"remark" => "",
			"#mapping" =>'NOW()'
		);

		if($dbc->Insert("bs_delivery_pack_items",$data)){
			echo json_encode(array(
				'success'=>true
			));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
