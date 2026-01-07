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

	if($dbc->HasRecord("bs_stock_adjust_type_bwd","name = '".$_POST['name']."' AND id !=".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Type Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'#type' => $_POST['type']
		);

		if($dbc->Update("bs_stock_adjust_type_bwd",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$type = $dbc->GetRecord("bs_stock_adjust_type_bwd","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"type-bwd-edit",$_POST['id'],array("types" => $type));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
