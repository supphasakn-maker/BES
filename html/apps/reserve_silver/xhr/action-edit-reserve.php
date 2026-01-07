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
	}else if($_POST['bar'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Plese input bar'
		));
	}else if(($_POST['weight_actual'] != $_POST['weight_lock']) && $_POST['weight_actual']!=""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Your Actual Weight must be same'
		));
	}else{
		$data = array(
			"lock_date" => $_POST['lock_date'],
			"#supplier_id" => $_POST['supplier_id'],
			"#discount" => $_POST['discount'],
			"#weight_lock" => $_POST['weight_lock'],
			"#weight_actual" => $_POST['weight_actual']!=""?$_POST['weight_actual']:"NULL",
			"#weight_fixed" => "NULL",
			"#weight_pending" => "NULL",
			"#defer" =>  "NULL",
			"#bar" =>  isset($_POST['bar'])?$_POST['bar']:"NULL",
			"#type" => $_POST['type'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#user" => $os->auth['id'],
			"#import_id" => "NULL",
			"brand" => $_POST['brand']
		);

		if($dbc->Update("bs_reserve_silver",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$reserve = $dbc->GetRecord("bs_reserve_silver","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"reserve-edit",$_POST['id'],array("reserves" => $reserve));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
