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

	if($_POST['code']== ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input code'
		));
	}else{
		
		$data = array(
			'#id' => "DEFAULT",
			"#packing_id" => "NULL",
			"code" => $_POST['code'],
			"#weight_expected" => "NULL",
			"#weight_actual" => $_POST['weight'],
			"#parent" => 'NULL',
			"#status" => 1,
			"#delivery_id" => 'NULL'
		);
		
		if($dbc->Insert("bs_packing_items",$data)){
			$packing_item_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true
			));
			foreach($_POST['items'] as $item){
				$data = array(
					'#parent' => $packing_item_id,
					'#status' => -1
				);
				$dbc->Update("bs_packing_items",$data,"id=".$item);
			}
			
			
			
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Insert"
			));
		}
	}

	$dbc->Close();
?>
