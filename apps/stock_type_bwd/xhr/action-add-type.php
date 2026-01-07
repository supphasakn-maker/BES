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


	if($dbc->HasRecord("bs_stock_adjust_type_bwd","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Type Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'#type' => $_POST['type']
		);

		if($dbc->Insert("bs_stock_adjust_type_bwd",$data)){
			$type_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $type_id
			));

			$type = $dbc->GetRecord("bs_stock_adjust_type_bwd","*","id=".$type_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"type-bwd-add",$type_id,array("types-bwd" => $type));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
