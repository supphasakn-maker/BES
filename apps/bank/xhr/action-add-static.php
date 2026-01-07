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

	if($_POST['start']==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please input start"
		));
	}else if($_POST['amount']==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please input amount"
		));
	}else if($_POST['title']==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please input title"
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			"#type" => $_POST['type'],
			"start" => $_POST['start'],
			"title" => $_POST['title'],
			"customer_name" => $_POST['customer_name'],
			"#amount" => $_POST['amount'],
		);
		
		if($_POST['end']!=""){
			$data['end'] = $_POST['end'];
		}else{
			$data['#end'] = 'NULL';
		}

		if($dbc->Insert("bs_finance_static_values",$data)){
			$static_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $static_id
			));

			$static = $dbc->GetRecord("bs_finance_static_values","*","id=".$static_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"static-add",$static_id,array("statics" => $static));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	

	$dbc->Close();
?>
