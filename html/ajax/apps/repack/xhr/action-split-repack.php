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

	if($_POST['split_new']=="NaN"){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input value'
		));
	}else{
		$item = $dbc->GetRecord("bs_packing_items","*","id=".$_POST['id']);
		
		for($i=0;$i<count($_POST['code']);$i++){
			$data = array(
				"#id" => "DEFAULT",
				"#production_id" => $item['production_id'],
				"code" => $_POST['code'][$i],
				"#weight_expected" => $_POST['weight_expected'][$i],
				"#weight_actual" => $_POST['weight_actual'][$i],
				"#parent" => $item['id'],
				"#status" => 0,
				"#delivery_id" => "NULL",
				"pack_type" => $_POST['pack_type'][$i],
				"pack_name" => $_POST['pack_name_split'][$i],
				"#created" => "NOW()"
			);
			$dbc->Insert("bs_packing_items",$data);
		}
		
		$data = array(
			"#status" => -1
		);

		if($dbc->Update("bs_packing_items",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$repack = $dbc->GetRecord("bs_packing_items","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"repack-split",$_POST['id'],array("repacks" => $repack));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
