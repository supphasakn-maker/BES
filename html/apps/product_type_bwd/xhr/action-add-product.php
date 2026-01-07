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


	if($dbc->HasRecord("bs_products_bwd","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Type Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
            'code' => $_POST['code'],
			'name' => $_POST['name'],
            '#updated' => 'NOW()',
            '#status' => 1
			
		);

		if($dbc->Insert("bs_products_bwd",$data)){
			$product_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $product_id
			));

			$type = $dbc->GetRecord("bs_products_bwd","*","id=".$product_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"product-bwd-add",$product_id,array("types-bwd" => $type));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
