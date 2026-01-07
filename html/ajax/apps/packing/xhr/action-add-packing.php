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
			
			'#id' => "DEFAULT",
			"#production_id" => $_POST['production_id'],
			"round" => $prodcution['round'],
			"date" => $_POST['date'],
			"time" => $_POST['time'],
			"#weight_peritem" => $_POST['weight_peritem'],
			"#total_item" => $_POST['total_item'],
			"#total_weight" => $_POST['total_weight'],
			"size" => $_POST['size'],
			"remark" => $_POST['remark'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#approver_weight" => $_POST['approver_weight'],
			"#approver_general" => $_POST['approver_general'],
			"#status" => 0,
		);

		if($dbc->Insert("bs_packings",$data)){
			$packing_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $packing_id
			));
			
			for($i=0;$i<count($_POST['item_code']);$i++){
				$data = array(
					'#id' => "DEFAULT",
					"#packing_id" => $packing_id,
					"code" => $_POST['item_code'][$i],
					"#weight_expected" => $_POST['item_weight'][$i],
					"#weight_actual" => $_POST['item_actual'][$i],
					"#parent" => 'NULL',
					"#status" => 0,
					"#delivery_id" => 'NULL'
				);
				$dbc->Insert("bs_packing_items",$data);
			}
			
			$packing = $dbc->GetRecord("bs_packings","*","id=".$packing_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"packing-add",$packing_id,array("packings" => $packing));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
