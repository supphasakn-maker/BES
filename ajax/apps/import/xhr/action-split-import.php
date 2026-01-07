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

	if($_POST['split']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Plese input splited number'
		));
	}else{
		$import = $dbc->GetRecord("bs_imports","*","id=".$_POST['id']);
		$data = array(
			"#id" => 'DEFAULT',
			"#supplier_id" => $import['supplier_id'],
			"#amount" => $_POST['split'],
			"type" => $import['type'],
			"delivery_date" => $import['delivery_date'],
			"delivery_by" => $import['delivery_by'],
			"comment" => addslashes($import['comment']),
			"created" => $import['created'],
			"updated" => $import['updated'],
			"#user" => $import['user'],
			"#status" => 0,
			"#submited" => "NULL",
			"#production_id" => "NULL",
			"#delivery_time" => "NULL",
			"#delivery_note" => "NULL",
			"#weight_in" => "NULL",
			"#weight_actual" => "NULL",
			"#weight_margin" => "NULL",
			"#bar" => "NULL",
			"#weight_bar" => "NULL",
			"#info_coa" => "NULL",
			"#info_coa_files" => "NULL",
			"#parent" => $import['id']
		);
		
		$dbc->Insert("bs_imports",$data);
		$data['#amount'] = $_POST['remain'];
		$dbc->Insert("bs_imports",$data);
		
		$data = array(
			'#updated' => 'NOW()',
			"#status" => 1
		);

		if($dbc->Update("bs_imports",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$import = $dbc->GetRecord("bs_imports","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"import-edit",$_POST['id'],array("bs_imports" => $import));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
		
	}

	$dbc->Close();
?>
