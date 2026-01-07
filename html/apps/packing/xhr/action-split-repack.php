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

		$pack_item = $dbc->GetRecord("bs_packing_items","*","id=".$_POST['id']);
		$data = array(
			'#status' => -1
		);

		if($dbc->Update("bs_packing_items",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			for($i=0;$i<count($_POST['item_code']);$i++){
				$data = array(
					'#id' => "DEFAULT",
					"#packing_id" => $pack_item['packing_id'],
					"code" => $_POST['item_code'][$i],
					"#weight_expected" => "NULL",
					"#weight_actual" => $_POST['item_weight'][$i],
					"#parent" => $pack_item['id'],
					"#status" => 1,
					"#delivery_id" => 'NULL'
				);
				$dbc->Insert("bs_packing_items",$data);
			}
			
			
			$repack = $dbc->GetRecord("bs_packing_items","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"repack-split",$_POST['id'],array("repacks" => $repack));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
