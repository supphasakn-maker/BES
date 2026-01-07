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


	if($_POST['round']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุจำนวนรอบ'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			"round" => $_POST['round'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#user" => $os->auth['id'],
			"remark" => addslashes($_POST['remark']),
			"#weight_in_safe" => $_POST['weight_in_safe'],
			"#weight_in_plate" => $_POST['weight_in_plate'],
			"#weight_in_nugget" => $_POST['weight_in_nugget'],
			"#weight_in_blacknugget" => $_POST['weight_in_blacknugget'],
			"#weight_in_whitedust" => $_POST['weight_in_whitedust'],
			"#weight_in_blackdust" => $_POST['weight_in_blackdust'],
			"#weight_in_refine" => $_POST['weight_in_refine'],
			"#weight_in_1" => $_POST['weight_in_1'],
			"#weight_in_2" => $_POST['weight_in_2'],
			"#weight_in_3" => $_POST['weight_in_3'],
			"#weight_in_4" => $_POST['weight_in_4'],
			"#weight_in_total" => $_POST['weight_in_total'],
			"#weight_out_safe" => $_POST['weight_out_safe'],
			"#weight_out_plate" => $_POST['weight_out_plate'],
			"#weight_out_nugget" => $_POST['weight_out_nugget'],
			"#weight_out_blacknugget" => $_POST['weight_out_blacknugget'],
			"#weight_out_whitedust" => $_POST['weight_out_whitedust'],
			"#weight_out_blackdust" => $_POST['weight_out_blackdust'],
			"#weight_out_refine" => $_POST['weight_out_refine'],
			"#weight_out_packing" => $_POST['weight_out_packing'],
			"#weight_out_total" => $_POST['weight_out_total'],
			"#weight_margin" => $_POST['weight_margin'],
			"#submited" => 'NULL',
			"delivery_license" => $_POST['delivery_license'],
			"delivery_driver" => $_POST['delivery_driver'],
			"delivery_time" => $_POST['delivery_time'],
			"approver_appointment" => $_POST['approver_appointment'],
			"type_material" => $_POST['type_material'],
			"type_work" => $_POST['type_work'],
			"type_thaicustoms_method" => $_POST['type_thaicustoms_method'],
			"#status" => 0
		);
		
		if(isset($_POST['approver_weight'])){
			$data["approver_weight"] = join(",",$_POST['approver_weight']);
		}else{
			$data["#approver_weight"] ="NULL";
		}
		
		if(isset($_POST['approver_general'])){
			$data["approver_general"] = join(",",$_POST['approver_general']);
		}else{
			$data["#approver_general"] ="NULL";
		}

		if($dbc->Insert("bs_productions",$data)){
			$produce_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $produce_id
			));
			
			
			
			$import_date = "";
			$import_weight_in = 0;
			$import_weight_actual = 0;
			$import_weight_margin = 0;
			$import_bar = 0;
			$import_bar_weight = 0;
			for($i=0;$i<count($_POST['import_id']);$i++){
				$import = $dbc->GetRecord("bs_imports","*","id=".$_POST['import_id'][$i]); 
				$dbc->Update("bs_imports",array(
					"#production_id" => $produce_id,
					"delivery_time" => $_POST['import_time'][$i],
					"delivery_note" => $_POST['import_delivery_note'][$i],
					"bar" => $_POST['import_bar'][$i],
					"weight_in" => $_POST['import_amount'][$i],
					"weight_margin" => $_POST['import_weight_margin'][$i],
					"weight_actual" => $_POST['import_weight_in'][$i],
					"weight_bar" => $_POST['import_weight_average'][$i],
					"type" => 1
				),"id=".$_POST['import_id'][$i]);
				
				$import_date = $import['delivery_date'];
				$import_weight_in += $_POST['import_amount'][$i];
				$import_weight_actual += $_POST['import_weight_in'][$i];
				$import_bar += $_POST['import_bar'][$i];
			}
			$import_weight_margin = $import_weight_in-$import_weight_actual;
			$import_bar_weight = $import_weight_actual/$import_bar;
			
			$dbc->Update("bs_productions",array(
				"import_date" => $import_date,
				"#import_weight_in" => $import_weight_in,
				"#import_weight_actual" => $import_weight_actual,
				"#import_weight_margin" => $import_weight_margin,
				"#import_bar" => $import_bar,
				"#import_bar_weight" => $import_bar_weight,
			),"id=".$produce_id);

			$produce = $dbc->GetRecord("bs_productions","*","id=".$produce_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"produce-add",$produce_id,array("bs_productions" => $produce));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
