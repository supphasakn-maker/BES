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


	if($dbc->HasRecord("bs_smg_rate_rollover","date = '".$_POST['date']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Interest Rollover Date is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'date' => $_POST['date'],
			'#rate_short' => $_POST['rate_short'],
			'#rate' => $_POST['rate'],
            '#interest' => $_POST['interest']
		);

		if($dbc->Insert("bs_smg_rate_rollover",$data)){
			$int_rate_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $int_rate_id
			));

			$int_rate = $dbc->GetRecord("bs_smg_rate_rollover","*","id=".$int_rate_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"int_rollover-add",$int_rate_id,array("bs_smg_rate_rollover" => $int_rate));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
