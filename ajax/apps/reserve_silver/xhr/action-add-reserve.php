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


	if($_POST['lock_date'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Plese input date'
		));
	}else if($_POST['weight_lock'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Plese input weight'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			"lock_date" => $_POST['lock_date'],
			"#supplier_id" => $_POST['supplier_id'],
			"#discount" => $_POST['discount'],
			"#weight_lock" => $_POST['weight_lock'],
			"#weight_actual" => "NULL",
			"#weight_fixed" => "NULL",
			"#weight_pending" => "NULL",
			"#defer" =>  "NULL",
			"#bar" =>  "NULL",
			"#type" => $_POST['type'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#user" => $os->auth['id'],
			"#import_id" => "NULL"
		);

		if($dbc->Insert("bs_reserve_silver",$data)){
			$reserve_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $reserve_id
			));

			$reserve = $dbc->GetRecord("bs_reserve_silver","*","id=".$reserve_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"reserve-add",$reserve_id,array("reserves" => $reserve));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
