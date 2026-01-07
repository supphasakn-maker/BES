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
		
		$json_data = array(
			"import" => array()
		);
		
		if(isset($_POST['import_time']))
		for($i=0;$i<count($_POST['import_time']);$i++){
			array_push($json_data['import'],array(
				"import_time" => $_POST['import_time'][$i],
				"import_number" => $_POST['import_number'][$i],
				"import_bar" => $_POST['import_bar'][$i],
				"import_weight_in" => $_POST['import_weight_in'][$i],
				"import_weight_actual" => $_POST['import_weight_actual'][$i],
				"import_weight_margin" => $_POST['import_weight_margin'][$i],
				"import_weight_bar" => $_POST['import_weight_bar'][$i]
			));
			
		}
		
		$data = array(
			"round" => $_POST['round'],
			'#updated' => 'NOW()',
			"remark" => addslashes($_POST['remark']),
			"#weight_in_safe" => ($_POST['weight_in_safe']!="")?$_POST['weight_in_safe']:"NULL",
			"#weight_in_plate" => ($_POST['weight_in_plate']!="")?$_POST['weight_in_plate']:"NULL",
			"#weight_in_nugget" => ($_POST['weight_in_nugget']!="")?$_POST['weight_in_nugget']:"NULL",
			"#weight_in_blacknugget" => ($_POST['weight_in_blacknugget']!="")?$_POST['weight_in_blacknugget']:"NULL",
			"#weight_in_whitedust" => ($_POST['weight_in_whitedust']!="")?$_POST['weight_in_whitedust']:"NULL",
			"#weight_in_blackdust" => ($_POST['weight_in_blackdust']!="")?$_POST['weight_in_blackdust']:"NULL",
			"#weight_in_refine" => ($_POST['weight_in_refine']!="")?$_POST['weight_in_refine']:"NULL",
			"#weight_in_1" => ($_POST['weight_in_1']!="")?$_POST['weight_in_1']:"NULL",
			"#weight_in_2" => ($_POST['weight_in_2']!="")?$_POST['weight_in_2']:"NULL",
			"#weight_in_3" => ($_POST['weight_in_3']!="")?$_POST['weight_in_3']:"NULL",
			"#weight_in_4" => ($_POST['weight_in_4']!="")?$_POST['weight_in_4']:"NULL",
			"#weight_in_total" => ($_POST['weight_in_total']!="")?$_POST['weight_in_total']:"NULL",
			"#weight_out_safe" => ($_POST['weight_out_safe']!="")?$_POST['weight_out_safe']:"NULL",
			"#weight_out_plate" => ($_POST['weight_out_plate']!="")?$_POST['weight_out_plate']:"NULL",
			"#weight_out_nugget" => ($_POST['weight_out_nugget']!="")?$_POST['weight_out_nugget']:"NULL",
			"#weight_out_blacknugget" => ($_POST['weight_out_blacknugget']!="")?$_POST['weight_out_blacknugget']:"NULL",
			"#weight_out_whitedust" => ($_POST['weight_out_whitedust']!="")?$_POST['weight_out_whitedust']:"NULL",
			"#weight_out_blackdust" => ($_POST['weight_out_blackdust']!="")?$_POST['weight_out_blackdust']:"NULL",
			"#weight_out_refine" => ($_POST['weight_out_refine']!="")?$_POST['weight_out_refine']:"NULL",
			"#weight_out_packing" => ($_POST['weight_out_packing']!="")?$_POST['weight_out_packing']:"NULL",
			"#weight_out_total" => ($_POST['weight_out_total']!="")?$_POST['weight_out_total']:"NULL",
			"#weight_margin" => ($_POST['weight_margin']!="")?$_POST['weight_margin']:"NULL",
			"delivery_license" => $_POST['delivery_license'],
			"delivery_driver" => $_POST['delivery_driver'],
			"type_material" => ($_POST['type_material']!="")?$_POST['type_material']:"NULL",
			"type_work" => ($_POST['type_work']!="")?$_POST['type_work']:"NULL",
			"type_thaicustoms_method" => ($_POST['type_thaicustoms_method']!="")?$_POST['type_thaicustoms_method']:"NULL",
			"#status" => 0,
			"data" => json_encode($json_data),
			"#product_id" => $_POST['product_id']
			
			
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
		
		if($_POST['delivery_time']!=""){
			$data["delivery_time"] =$_POST['delivery_time'];
		}else{
			$data["#delivery_time"] ="NULL";
		}
		
		if($_POST['approver_appointment']!=""){
			$data["approver_appointment"] =$_POST['approver_appointment'];
		}else{
			$data["#approver_appointment"] ="NULL";
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
