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
		$plan = $dbc->GetRecord("bs_incoming_plans","*","id=".$_POST['id']);
		$data = array(
			"#id" => "DEFAULT",
			"#import_id" => $plan['import_id'],
			"#created" => "NOW()",
			"#updated" => "NOW()",
			"#user_id" => $os->auth['id'],
			"import_date" => $plan['import_date'],
			"import_brand" => $plan['import_brand'],
			"import_lot" => $plan['import_lot'],
			"factory" => $plan['factory'],
			"#product_type_id" => $plan['product_type_id'],
			"coa" => $plan['coa'],
			"remark" => $plan['remark'],
			"#parent" => $plan['id'],
			"#status" => 1
		);
		
		foreach($_POST['split'] as $splited){
			$data['#amount'] = $splited;
			$dbc->Insert("bs_incoming_plans",$data);
		}
		
		$data = array(
			'#updated' => 'NOW()',
			"#status" => 0
		);

		if($dbc->Update("bs_incoming_plans",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$plan = $dbc->GetRecord("bs_incoming_plans","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"plant-split",$_POST['id'],array("bs_incoming_plans" => $plan));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
		
	}

	$dbc->Close();
?>
