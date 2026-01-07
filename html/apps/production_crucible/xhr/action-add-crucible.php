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
    }else{
		$counter = 0;
		for($i=$_POST['start'];$i<$_POST['start']+$_POST['end'];$i++){
			$code = $i;
			if($dbc->HasRecord("bs_productions_crucible","round = '".$code."'")){
				array_push($aRedundant,$code);
			}else{
				$data = array(
					"#id" => "DEFAULT",
					"round" => $code,
                    "#created" => "NOW()",
                    "#updated" => "NOW()",
                    'date' => $_POST['date'],
                    '#user#' => $_SESSION['auth']['user_id'],
					"#status" => 0
				);
		
				$dbc->Insert("bs_productions_crucible",$data);
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
