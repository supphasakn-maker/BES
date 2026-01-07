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

	if(count($_POST['split']) < 2){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'ต้องมากกว่า 2 ส่วนขึ้นไป'
		));
	}else{
		$import = $dbc->GetRecord("bs_imports","*","id=".$_POST['id']);
		$data = array(
			"#id" => 'DEFAULT',
			"#import_id" => $import['id'],
			"created" => $import['created'],
			"#updated" => 'NOW()',
			"remark" => ''
		);
		
		foreach($_POST['split'] as $splited){
			$data['#amount'] = $splited;
			$dbc->Insert("bs_import_usd_splited",$data);
		}
		
		

		echo json_encode(array(
			'success'=>true
		));
		$import = $dbc->GetRecord("bs_imports","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"import-splited",$_POST['id'],array("bs_imports" => $import));
		
		
	}

	$dbc->Close();
?>
