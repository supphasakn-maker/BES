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



	$data = array(
		"#status" => 0,
	);
	
	if($dbc->Update("bs_packing_items",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
	

	$dbc->Delete("bs_pmr_pack_items","item_id=".$_POST['id']);
	}else{
	echo json_encode(array(
		'success'=>false,
		'msg' => "No Change"
	));
	}

	
	$dbc->Close();
?>
