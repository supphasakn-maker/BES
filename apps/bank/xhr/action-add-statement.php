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
			'#id' => "DEFAULT",
			"#bank_id" => $_POST['bank_id'],
			"date" => $_POST['date'],
			"#type" => $_POST['type'],
			"#amount" => $_POST['type']==1?-$_POST['amount']:$_POST['amount'],
			"#balance" => $_POST['balance'],
			"narrator" => addslashes($_POST['narrator']),
			"#payment_id" => 'NULL',
			"#status" => 1,
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#approved" => 'NULL'
		);

		if($dbc->Insert("bs_bank_statement",$data)){
			$statement_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $statement_id
			));

			$statement = $dbc->GetRecord("bs_bank_statement","*","id=".$statement_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"statement-add",$statement_id,array("statements" => $statement));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	

	$dbc->Close();
?>
