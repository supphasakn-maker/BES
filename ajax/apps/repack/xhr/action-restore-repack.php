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
	
	if($_POST['removable']=="false"){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'ไม่สามารถลบได้เนื่องจากเงื่อนไขไม่ครบ'
		));
	}else{
		
		foreach($_POST['restore'] AS $item_id){
			$data = array(
				"#parent" => "NULL",
				"#status" => 0
			);
			$dbc->Update("bs_packing_items",$data,"id=".$item_id);
		}
		
		foreach($_POST['remove'] AS $item_id){
			$dbc->Delete("bs_packing_items","id=".$item_id);
		}
		
		echo json_encode(array(
			'success'=>true
		));
	}

	$dbc->Close();
?>
