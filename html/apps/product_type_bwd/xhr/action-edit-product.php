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

	if($dbc->HasRecord("bs_products_bwd","name = '".$_POST['name']."' AND id !=".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Type Name is already exist.'
		));
	}else{
		$data = array(
			'code' => $_POST['code'],
			'name' => $_POST['name']
		);

		if($dbc->Update("bs_products_bwd",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$product = $dbc->GetRecord("bs_products_bwd","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"product-bwd-edit",$_POST['id'],array("types" => $product));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
