<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_purchase_usd.id",
		"bank" => "bs_purchase_usd.bank",
		"type" => "bs_purchase_usd.type",
		"amount" => "FORMAT(bs_purchase_usd.amount,2)",
		"rate_exchange" => "bs_purchase_usd.rate_exchange",
		"date" => "bs_purchase_usd.date",
		"comment" => "bs_purchase_usd.comment",
		"method" => "bs_purchase_usd.method",
		"ref" => "bs_purchase_usd.ref",
		"user" => "bs_purchase_usd.user",
		"status" => "bs_purchase_usd.status",
		"confirm" => "bs_purchase_usd.confirm",
		"created" => "bs_purchase_usd.created",
		"updated" => "bs_purchase_usd.updated",
		"parent" => "bs_purchase_usd.parent",
		"bank_date" => "bs_purchase_usd.bank_date",
		"premium_start" => "bs_purchase_usd.premium_start",
		"premium" => "bs_purchase_usd.premium",
		"transfer_id" => "bs_purchase_usd.transfer_id",
		"fw_contract_no" => "bs_purchase_usd.fw_contract_no",
		"unpaid" => "bs_purchase_usd.unpaid",
		"value" => "FORMAT(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange,2)"
	);
	
	
	$where = "bs_mapping_usd_purchases.id IS NULL
			AND bs_purchase_usd.parent IS NULL  AND YEAR(date) > 2021";
	
	if(isset($_GET['date_filter'])){
		$where .= " AND bs_purchase_usd.date = '".$_GET['date_filter']."'";
	}

	$table = array(
		"index" => "id",
		"name" => "bs_purchase_usd",
		"join" => array(
			array(
				"field" => "id",
				"table" => "bs_mapping_usd_purchases",
				"with" => "purchase_id",
			)
		),"where" => $where
	);
	
	

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	
	$total_remain = 0;
	
	$aaData = array();
	$data = $dbc->GetResult();
	$sql = "SELECT * FROM bs_mapping_usd_purchases WHERE mapping_id IS NULL";
	$rst = $dbc->Query($sql);
	while($mapping = $dbc->Fetch($rst)){
		$purchase = $dbc->GetRecord("bs_purchase_usd","*","id=".$mapping['purchase_id']);
		$value = $purchase['amount']*$purchase['rate_exchange'];
		
		array_push($aaData,array(
			"DT_RowId" => $purchase['id'],
			"id" => $purchase['id'],
			"bank" => $purchase['bank'],
			"type" => $purchase['type'],
			"amount" => number_format($purchase['amount'],2),
			"rate_exchange" => $purchase['rate_exchange'],
			"total" => number_format($value,2),
			"date" => $purchase['date'],
			"remain" => number_format($mapping['amount'],2),
			"mapping_item_id" => $mapping['id'],
			"value" => number_format($purchase['amount']*$purchase['rate_exchange'],2),
		));
		
		$total_remain += $mapping['amount'];
	}
	
	for($i=0;$i<count($data['aaData']);$i++){
		array_push($aaData,$data['aaData'][$i]);
	}
	
	$data['aaData'] = $aaData;
	
	$sql = "SELECT SUM(bs_purchase_usd.amount) FROM bs_purchase_usd 
	LEFT JOIN bs_mapping_usd_purchases ON bs_mapping_usd_purchases.purchase_id = bs_purchase_usd.id 
	WHERE ".$where;
	
	$rst = $dbc->Query($sql);
	$line = $dbc->Fetch($rst);
	
	$data['total'] = array(
		"remian_unmatch" => $line[0],
		"remain_matching" => $total_remain,
		"remain_total" => $line[0]+$total_remain
	);
	
	
	echo json_encode($data);

	$dbc->Close();

?>
