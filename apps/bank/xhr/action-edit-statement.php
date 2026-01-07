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
			"date" => $_POST['date'],
			"#type" => $_POST['type'],
			"#amount" => $_POST['type']==1?-$_POST['amount']:$_POST['amount'],
			"#balance" => $_POST['balance'],
			"narrator" => addslashes($_POST['narrator']),
			'#updated' => 'NOW()',
		);

		if($dbc->Update("bs_bank_statement",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$statement = $dbc->GetRecord("bs_bank_statement","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"statement-edit",$_POST['id'],array("statements" => $statement));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
