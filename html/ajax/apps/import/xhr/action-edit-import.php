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

	if($_POST['delivery_date']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Plese input delivery_date'
		));
	}else{
		$files = array();
		
		if(isset($_POST['path'])){
			$files = $_POST['path'];
		}
		
		$data = array(
			"type" => $_POST['type'],
			"delivery_date" => $_POST['delivery_date'],
			"delivery_by" => $_POST['delivery_by'],
			"comment" => $_POST['comment'],
			"info_coa" => $_POST['info_coa'],
			'#updated' => 'NOW()',
			'info_coa_files' => json_encode($files)
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
