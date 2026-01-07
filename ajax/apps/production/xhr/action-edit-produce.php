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
			'#updated' => 'NOW()',
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
			"delivery_license" => $_POST['delivery_license'],
			"delivery_driver" => $_POST['delivery_driver'],
			"delivery_time" => $_POST['delivery_time'],
			"approver_appointment" => $_POST['approver_appointment'],
			"type_material" => $_POST['type_material'],
			"type_work" => $_POST['type_work'],
			"type_thaicustoms_method" => $_POST['type_thaicustoms_method'],
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
		
		if(isset($_POST['img_path'])){
			$aImg = array();
			for($i=0;$i<count($_POST['img_path']);$i++){
				array_push($aImg,array(
					"path" => $_POST['img_path'][$i],
					"desc" => $_POST['img_desc'][$i]
				));
			}
			$data["imgs"] = json_encode($aImg,JSON_UNESCAPED_UNICODE);
		}
		

		if($dbc->Update("bs_productions",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			
			$produce_id = $_POST['id'];
			$dbc->Update("bs_imports",array(
				"#production_id" => "NULL",
				"#delivery_time" => "NULL",
				"#delivery_note" => "NULL",
				"#weight_in" => "NULL",
				"#weight_actual" => "NULL",
				"#weight_margin" => "NULL",
				"#bar" => "NULL",
				"#weight_bar" => "NULL",
				"#type" => 0
			),"production_id=".$produce_id);
			
			$import_date = "";
			$import_weight_in = 0;
			$import_weight_actual = 0;
			$import_weight_margin = 0;
			$import_bar = 0;
			$import_bar_weight = 0;
			if(isset($_POST['import_id']))
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
			if($import_bar==0){
				$import_bar_weight = 0;
			}else{
				$import_bar_weight = $import_weight_actual/$import_bar;
			}
			
			$data = array(
				"#import_weight_in" => $import_weight_in,
				"#import_weight_actual" => $import_weight_actual,
				"#import_weight_margin" => $import_weight_margin,
				"#import_bar" => $import_bar,
				"#import_bar_weight" => $import_bar_weight,
			);
			
			if($import_date==""){
				$data["#import_date"]= "NULL";
			}else{
				$data["import_date"]= $import_date;
			}
			
			
			$dbc->Update("bs_productions",$data,"id=".$produce_id);
			
			
			
			
			$produce = $dbc->GetRecord("bs_productions","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"produce-edit",$_POST['id'],array("bs_productions" => $produce));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
