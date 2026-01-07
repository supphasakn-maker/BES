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
            "code" => $_POST['code'],
            "name" => $_POST['name'],
            "type" => $_POST['type'],
			"#product_id" => $_POST['product_id'],
            "user" => $os->auth['id'],
			'#updated' => 'NOW()',          
            "#status" => $_POST['status'],			
		);

		if($dbc->Update("bs_products_type",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$type = $dbc->GetRecord("bs_products_type","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"product-type-bwd-edit",$_POST['id'],array("product-type" => $type));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
