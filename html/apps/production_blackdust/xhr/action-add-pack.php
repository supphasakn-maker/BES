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
	
	$aCreated = array();
	$aRedundant = array();


	if($_POST['start']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input data!'
		));
	}else if($_POST['end']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input data!'
		));
	}else if($_POST['end']>1000){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'จำนวนต้องไม่เกิน 1000'
		));
	}else if($_POST['weight_expected']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input data!'
		));
	}else{
		$counter = 0;
		for($i=$_POST['start'];$i<$_POST['start']+$_POST['end'];$i++){
			$code = $_POST['prefix'].$i;
			if($dbc->HasRecord("bs_packing_items","code = '".$code."'")){
				array_push($aRedundant,$code);
			}else{
				$data = array(
					"#id" => "DEFAULT",
					"#production_id" => $_POST['id'],
					"code" => $code,
					"pack_name" => $_POST['pack_name'],
					"pack_type" => $_POST['pack_type'],
					"#weight_actual" => "NULL",
					"#weight_expected" => $_POST['weight_expected'],
					"#parent" => "NULL",
					"#status" => 0,
					"#delivery_id" => "NULL",
					"#created" => "NOW()"
				);
		
				$dbc->Insert("bs_packing_items",$data);
				array_push($aCreated,$code);
			}
			
		
			$counter++;
		}
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> $counter,
			'created' => $aCreated,
			'redundant' => $aRedundant
		));

	}

	$dbc->Close();
?>
