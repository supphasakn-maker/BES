<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	if($dbc->HasRecord("bs_products","code = '".$_POST['code']."' AND id != ".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'product Code is already exist.'
		));
	}else{
		$data = array(
			"code" => $_POST['code'],
			"name" => $_POST['name'],
			"detail" => addslashes($_POST['detail']),
			'#updated' => "NOW()"
		);
		
		if($dbc->Update("bs_products",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$product = $dbc->GetRecord("bs_products","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"product-edit",$_POST['id'],array("bs_products" => $product));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	
	$dbc->Close();
?>