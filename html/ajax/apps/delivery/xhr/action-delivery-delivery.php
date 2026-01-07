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
	
	$aError = array();
	
	
	if(count($aError)>0){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'กรองข้อมูลไม่ถูกต้อง',
			'error' => $aError
		));
	}else{
		for($i=0;$i<count($_POST['emp_driver']);$i++){
			$data = array(
				"#id" =>'DEFAULT',
				"#delivery_id" => $_POST['id'],
				"#emp_driver" => $_POST['emp_driver'][$i],
				"truck_type" => $_POST['truck_type'][$i],
				"truck_license" => $_POST['truck_license'][$i],
				"time_departure" => $_POST['time_departure'][$i],
				"time_arrive" => $_POST['time_arrive'][$i]
			);
			$dbc->Insert("bs_deliveries_drivers",$data);
		}
		
		echo json_encode(array(
			'success'=>true
		));
	}

	$dbc->Close();
?>
