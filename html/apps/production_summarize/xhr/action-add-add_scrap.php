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

	$date2 = date("Y-m-d");

	$production = $dbc->GetRecord("bs_productions","*","id=".$_POST['round']);

    if($_POST['weight_expected']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input น้ำหนัก!'
		));
    }else{
        $data = array(
            '#id' => "DEFAULT",
            '#production_id' => $_POST['round'],
            'pack_name' => $_POST['pack_name'],
            'pack_type' => 'เศษ',
            'weight_actual' => $_POST['weight_expected'],
            '#weight_expected' => $_POST['weight_expected'],
            "#parent" => "NULL",
            "#status" => 0,
            "#delivery_id" => "NULL",
            "#created" => "NOW()",
			"submited" => "$date2",
			'#product_id' => $_POST['product_id'],
        );
        if($dbc->Insert("bs_scrap_items",$data)){
			$scale_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $scale_id
			));
		$round = sprintf($scale_id);
		$dbc->Update("bs_scrap_items",array("code"=>$round),"id=".$scale_id);
		
		if($_POST['pack_name'] == "เม็ดเสียรอการผลิต"){
		$data2 = array(
			'#weight_out_safe' => $_POST['weight_expected'],
			'#weight_out_total' => $_POST['weight_expected'],
			'#weight_margin' => $_POST['weight_expected']
		);
		$dbc->Update("bs_productions",$data2,"id=".$_POST['round']);
		}else{
			$data3 = array(
				'#weight_out_refine' => $_POST['weight_expected'],
			);
			$dbc->Update("bs_productions",$data3,"id=".$_POST['round']);
		}

        $scale= $dbc->GetRecord("bs_scrap_items","*","id=".$scale_id);
        $os->save_log(0,$_SESSION['auth']['user_id'],"bs_scrap_items-add",$scale_id,array("bs_scrap_items" => $scale));
        }else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
?>