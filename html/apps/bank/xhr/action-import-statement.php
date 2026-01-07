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

	if($_POST['bank_id']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุจำนวน'
		));
	}else{
		
		$counter  = 0;
		
		for($i=$_POST['start'];$i<count($_POST['table']);$i++){
			
			if($_POST['table'][$i][1]!=""){
				$type = 1;
				$amount = $_POST['table'][$i][1];
			}else{
				$type = 2;
				$amount = $_POST['table'][$i][2];
			}
			
			$date = strtotime($_POST['table'][$i][0]);
			
			$data = array(
				'#id' => "DEFAULT",
				"#bank_id" => $_POST['bank_id'],
				"date" => date("Y-m-d",$date),
				"#type" => $type,
				"#amount" => $type==1?-$amount:$amount,
				"#balance" => $_POST['table'][$i][3],
				"narrator" => addslashes($_POST['table'][$i][4]),
				"#payment_id" => 'NULL',
				"#status" => 1,
				'#created' => 'NOW()',
				'#updated' => 'NOW()',
				"#approved" => 'NULL'
			);
			
			
			
			
			
			
			$dbc->Insert("bs_bank_statement",$data);
			$statement_id = $dbc->GetID();
			
			$counter++;
		}
		
		echo json_encode(array(
			'success'=>true,
			'msg'=>'Imported ' . $counter . 'รายการ'
		));
		
		

		
	}

	$dbc->Close();
?>
