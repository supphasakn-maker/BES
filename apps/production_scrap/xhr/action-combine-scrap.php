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
	
	if($_POST['code']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input value'
		));
	}else if($dbc->HasRecord("bs_scrap_items","code = '".$_POST['code']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Repack Name is already exist.'
		));
	}else{
		
		$data = array(
			"#id" => "DEFAULT",
			"#production_id" => "NULL",
			"code" =>  $_POST['code'],
			"#weight_expected" => $_POST['weight_expected'],
			"#weight_actual" => $_POST['weight_actual'],
			"#parent" => "NULL",
			"#status" => 0,
			"#delivery_id" => "NULL",
			"pack_type" => $_POST['pack_type'],
			"pack_name" => 'เม็ดเสียรอการผลิต',
			"#created" => "NOW()",
			"#product_id" =>  $_POST['products']
		);

		if($dbc->Insert("bs_scrap_items",$data)){
			$pack_combine_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true
			));
			
			foreach($_POST['pack_id'] as $id){
				$data = array(
					'#status' => -2,
					'#parent' => $pack_combine_id
				);
				$dbc->Update("bs_scrap_items",$data,"id=".$id);
			}
			
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
