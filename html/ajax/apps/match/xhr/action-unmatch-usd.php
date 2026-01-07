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

	$dbc->Delete("bs_mapping_usd","id=".$_POST['id']);

	# purchase
	$usd_mapping = $dbc->GetRecord("bs_mapping_usd_spots","*","mapping_id=".$_POST['id']);
	$dbc->Delete("bs_mapping_usd_spots","mapping_id=".$_POST['id']);
	
	$is_mapping = $dbc->HasRecord("bs_mapping_usd_spots","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NOT NULL");
	$is_remain = $dbc->HasRecord("bs_mapping_usd_spots","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NULL");
	
	if($is_mapping && $is_remain){// Case #1 Have Remain Record With Mapping Record
		$use_remain = $dbc->GetRecord("bs_mapping_usd_spots","*","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NULL");
		$dbc->Update("bs_mapping_usd_spots",array("#amount"=>$usd_mapping['amount']+$use_remain['amount']),"id=".$use_remain['id']);
	}else if(!$is_mapping && $is_remain){// Case #2 Have Remain Record Without Mapping
		$use_remain = $dbc->GetRecord("bs_mapping_usd_spots","*","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NULL");
		$dbc->Delete("bs_mapping_usd_spots","id=".$use_remain['id']);
	}else if($is_mapping && !$is_remain){// Case #3 Have Mapping Record without Remain
		$data = array(
			"#id" => 'DEFAULT',
			"#mapping_id" => 'NULL',
			"#purchase_id" => $usd_mapping['purchase_id'],
			"#amount" => $usd_mapping['amount']
		);
		$dbc->Insert("bs_mapping_usd_spots",$data);
	}
	
	
	
	
	# Purchase
	
	$usd_mapping = $dbc->GetRecord("bs_mapping_usd_purchases","*","mapping_id=".$_POST['id']);
	$dbc->Delete("bs_mapping_usd_purchases","mapping_id=".$_POST['id']);
	
	$is_mapping = $dbc->HasRecord("bs_mapping_usd_purchases","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NOT NULL");
	$is_remain = $dbc->HasRecord("bs_mapping_usd_purchases","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NULL");
	
	if($is_mapping && $is_remain){// Case #1 Have Remain Record With Mapping Record
		$use_remain = $dbc->GetRecord("bs_mapping_usd_purchases","*","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NULL");
		$dbc->Update("bs_mapping_usd_purchases",array("#amount"=>$usd_mapping['amount']+$use_remain['amount']),"id=".$use_remain['id']);
	}else if(!$is_mapping && $is_remain){// Case #2 Have Remain Record Without Mapping
		$use_remain = $dbc->GetRecord("bs_mapping_usd_purchases","*","purchase_id = ".$usd_mapping['purchase_id']." AND mapping_id IS NULL");
		$dbc->Delete("bs_mapping_usd_purchases","id=".$use_remain['id']);
	}else if($is_mapping && !$is_remain){// Case #3 Have Mapping Record without Remain
		$data = array(
			"#id" => 'DEFAULT',
			"#mapping_id" => 'NULL',
			"#purchase_id" => $usd_mapping['purchase_id'],
			"#amount" => $usd_mapping['amount']
		);
		$dbc->Insert("bs_mapping_usd_purchases",$data);
	}

	
	echo json_encode(array(
		'success'=>true
	));
	

	$dbc->Close();
?>
