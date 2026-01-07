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


	if($dbc->HasRecord("bs_products_type","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Type Name is already exist.'
		));
	}else if($_POST['code']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Code should not empty!'
		));
	}else if($_POST['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Name should not empty!'
		));
	}else if($_POST['product_id']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Product should not empty!'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
            'code' => $_POST['code'],
			'name' => $_POST['name'],
            'type' => $_POST['type'],
            '#product_id' => $_POST['product_id'],
			"#user" => $os->auth['id'],
            '#created' => 'NOW()',          
            '#updated' => 'NOW()',      
            '#status' => $_POST['status'],    
		);

		if($dbc->Insert("bs_products_type",$data)){
			$type_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $type_id
			));

			$type = $dbc->GetRecord("bs_products_type","*","id=".$type_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"product-type-bwd-add",$type_id,array("product-types-bwd" => $type));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
