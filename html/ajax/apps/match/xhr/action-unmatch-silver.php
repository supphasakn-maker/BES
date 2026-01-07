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

	$dbc->Delete("bs_mapping_silvers","id=".$_POST['id']);

	# Order
	$silver_mapping = $dbc->GetRecord("bs_mapping_silver_orders","*","mapping_id=".$_POST['id']);
	$dbc->Delete("bs_mapping_silver_orders","mapping_id=".$_POST['id']);
	
	$is_mapping = $dbc->HasRecord("bs_mapping_silver_orders","order_id = ".$silver_mapping['order_id']." AND mapping_id IS NOT NULL");
	$is_remain = $dbc->HasRecord("bs_mapping_silver_orders","order_id = ".$silver_mapping['order_id']." AND mapping_id IS NULL");
	
	if($is_mapping && $is_remain){// Case #1 Have Remain Record With Mapping Record
		$silver_remain = $dbc->GetRecord("bs_mapping_silver_orders","*","order_id = ".$silver_mapping['order_id']." AND mapping_id IS NULL");
		$dbc->Update("bs_mapping_silver_orders",array("#amount"=>$silver_mapping['amount']+$silver_remain['amount']),"id=".$silver_remain['id']);
	}else if(!$is_mapping && $is_remain){// Case #2 Have Remain Record Without Mapping
		$silver_remain = $dbc->GetRecord("bs_mapping_silver_orders","*","order_id = ".$silver_mapping['order_id']." AND mapping_id IS NULL");
		$dbc->Delete("bs_mapping_silver_orders","id=".$silver_remain['id']);
	}else if($is_mapping && !$is_remain){// Case #3 Have Mapping Record without Remain
		$data = array(
			"#id" => 'DEFAULT',
			"#mapping_id" => 'NULL',
			"#order_id" => $silver_mapping['order_id'],
			"#amount" => $silver_mapping['amount']
		);
		$dbc->Insert("bs_mapping_silver_orders",$data);
	}
	
	
	
	
	# Purchase
	
	$silver_mapping = $dbc->GetRecord("bs_mapping_silver_purchases","*","mapping_id=".$_POST['id']);
	$dbc->Delete("bs_mapping_silver_purchases","mapping_id=".$_POST['id']);
	
	$is_mapping = $dbc->HasRecord("bs_mapping_silver_purchases","purchase_id = ".$silver_mapping['purchase_id']." AND mapping_id IS NOT NULL");
	$is_remain = $dbc->HasRecord("bs_mapping_silver_purchases","purchase_id = ".$silver_mapping['purchase_id']." AND mapping_id IS NULL");
	
	if($is_mapping && $is_remain){// Case #1 Have Remain Record With Mapping Record
		$silver_remain = $dbc->GetRecord("bs_mapping_silver_purchases","*","purchase_id = ".$silver_mapping['purchase_id']." AND mapping_id IS NULL");
		$dbc->Update("bs_mapping_silver_purchases",array("#amount"=>$silver_mapping['amount']+$silver_remain['amount']),"id=".$silver_remain['id']);
	}else if(!$is_mapping && $is_remain){// Case #2 Have Remain Record Without Mapping
		$silver_remain = $dbc->GetRecord("bs_mapping_silver_purchases","*","purchase_id = ".$silver_mapping['purchase_id']." AND mapping_id IS NULL");
		$dbc->Delete("bs_mapping_silver_purchases","id=".$silver_remain['id']);
	}else if($is_mapping && !$is_remain){// Case #3 Have Mapping Record without Remain
		$data = array(
			"#id" => 'DEFAULT',
			"#mapping_id" => 'NULL',
			"#purchase_id" => $silver_mapping['purchase_id'],
			"#amount" => $silver_mapping['amount']
		);
		$dbc->Insert("bs_mapping_silver_purchases",$data);
	}

	
	echo json_encode(array(
		'success'=>true
	));
	

	$dbc->Close();
?>
