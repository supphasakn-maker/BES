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

	if($_POST['production_id']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Have to put prodcution round!'
		));
	}else{
		$prodcution = $dbc->GetRecord("bs_productions","*","id=".$_POST['production_id']);
		$data = array(
			"#production_id" => $_POST['production_id'],
			"round" => $prodcution['round'],
			"date" => $_POST['date'],
			"time" => $_POST['time'],
			"#weight_peritem" => $_POST['weight_peritem'],
			"#total_item" => $_POST['total_item'],
			"#total_weight" => $_POST['total_weight'],
			"size" => $_POST['size'],
			"remark" => $_POST['remark'],
			'#updated' => 'NOW()',
			"#approver_weight" => $_POST['approver_weight'],
			"#approver_general" => $_POST['approver_general']
		);

		if($dbc->Update("bs_packings",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			
			$dbc->Delete("bs_packing_items","packing_id=".$_POST['id']);
			
			for($i=0;$i<count($_POST['item_code']);$i++){
				$data = array(
					'#id' => "DEFAULT",
					"#packing_id" => $_POST['id'],
					"code" => $_POST['item_code'][$i],
					"#weight_expected" => $_POST['item_weight'][$i],
					"#weight_actual" => $_POST['item_actual'][$i],
					"#parent" => 'NULL',
					"#status" => 0,
					"#delivery_id" => 'NULL'
				);
				$dbc->Insert("bs_packing_items",$data);
			}
			
			$packing = $dbc->GetRecord("bs_packings","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"packing-edit",$_POST['id'],array("packings" => $packing));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
