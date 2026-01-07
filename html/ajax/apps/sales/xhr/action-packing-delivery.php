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

	$delivery = $dbc->GetRecord("bs_deliveries","*","id=".$_POST['id']);
	
	$total = 0;
	for($i=0;$i<count($_POST['totaleach']);$i++){
		$total += $_POST['totaleach'][$i];
	}
	
	if($total != $delivery['amount']){
		echo json_encode(array(
			'success'=>false,
			'msg'=>"จำนวนกิโลไม่ตรงกับคำสั่งซื้อ"
		));
	}else{
		
		$dbc->Delete("bs_delivery_items","delivery_id=".$delivery['id']);
		
		for($i=0;$i<count($_POST['name']);$i++){
			$data = array(
				"#id" => "DEFAULT",
				"delivery_id" => $delivery['id'],
				"name" => $_POST['name'][$i],
				"#size" => $_POST['size'][$i],
				"#amount" => $_POST['amount'][$i],
				"comment" => $_POST['comment'][$i],
				"#status" => 0
			);
			$dbc->Insert("bs_delivery_items",$data);
		}
		
		$dbc->Update("bs_deliveries",array("#status"=>1),"id=".$delivery['id']);
		
		echo json_encode(array(
			'success'=>true
		));
	}

	$dbc->Close();
?>
