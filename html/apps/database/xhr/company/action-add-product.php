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
	
	if($dbc->HasRecord("bs_products","code = '".$_POST['code']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'product Code is already exist.'
		));
	}else if($_POST['code']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert product code'
		));
	}else{
		$data = array(
			"#id" => "DEFAULT",
			"code" => $_POST['code'],
			"name" => $_POST['name'],
			"detail" => addslashes($_POST['detail']),
			"#created" => "NOW()",
			"#updated" => "NOW()",
			"#status" => 1,
			"imgs" => "{}",
		);
		
		if($dbc->Insert("bs_products",$data)){
			$product_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $product_id
			));
			
			$product = $dbc->GetRecord("bs_products","*","id=".$product_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"product-add",$product_id,array("bs_products" => $product));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>