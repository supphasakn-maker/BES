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
	
	
	$sql = "SELECT DISTINCT(purchase_id) FROM bs_mapping_usd_spots";
	$rst = $dbc->Query($sql);
	while($map_item = $dbc->Fetch($rst)){
		$purchase = $dbc->GetRecord("bs_purchase_spot","*","id=".$map_item[0]);
		$item = $dbc->GetRecord("bs_mapping_usd_spots","SUM(amount)","purchase_id=".$map_item[0]);
		$amount = $purchase['amount']*($purchase['rate_pmdc']+$purchase['rate_spot'])*32.1507;
		
		if(number_format($amount,1) != number_format($item[0],1)){
			$remain = $amount - $item[0];
			$sql_new = "INSERT INTO bs_mapping_usd_spots VALUES(DEFAULT,NULL,".$purchase['id'].",".$remain.",NULL);";
			echo $sql_new;
			echo "\n";
		}
		
		if(!$dbc->HasRecord("bs_mapping_usd_spots","mapping_id IS NOT NULL AND purchase_id = ".$purchase['id'])){
			$sql_remove = "DELETE FROM bs_mapping_usd_spots WHERE purchase_id =".$map_item[0];
			echo $sql_remove;
			echo "\n";
		}
	}
	
	$sql = "SELECT DISTINCT(purchase_id) FROM bs_mapping_usd_purchases";
	$rst = $dbc->Query($sql);
	while($map_item = $dbc->Fetch($rst)){
		$purchase = $dbc->GetRecord("bs_purchase_usd","*","id=".$map_item[0]);
		$item = $dbc->GetRecord("bs_mapping_usd_purchases","SUM(amount)","purchase_id=".$map_item[0]);
		
		
		if(!is_null($purchase['amount']))
		if($purchase['amount'] != $item[0]){
			//echo $purchase['amount'].",".$item[0];
			$remain = $purchase['amount'] - $item[0];
			$sql_new = "INSERT INTO bs_mapping_usd_purchases VALUES(DEFAULT,NULL,".$purchase['id'].",".$remain.");";
			echo $sql_new;
			echo "\n";
		}
		
		if(!is_null($purchase['id']))
		if(!$dbc->HasRecord("bs_mapping_usd_purchases","mapping_id IS NOT NULL AND purchase_id = ".$purchase['id'])){
			$sql_remove = "DELETE FROM bs_mapping_usd_purchases WHERE purchase_id =".$map_item[0];
			echo $sql_remove;
			echo "\n";
		}
		
	}
	
	
	$sql = "SELECT DISTINCT(order_id) FROM bs_mapping_silver_orders";
	$rst = $dbc->Query($sql);
	while($map_item = $dbc->Fetch($rst)){
		
		$order = $dbc->GetRecord("bs_orders","*","id=".$map_item[0]);
		$item = $dbc->GetRecord("bs_mapping_silver_orders","SUM(amount)","order_id=".$map_item[0]);
		$amount = $order['amount'];
		
		if($amount!= $item[0]){
			$remain = $amount - $item[0];
			$sql_new = "INSERT INTO bs_mapping_silver_orders VALUES(DEFAULT,NULL,".$order['id'].",".$remain.");";
			echo $sql_new;
			echo "\n";
		}
		
		if(!$dbc->HasRecord("bs_mapping_silver_orders","mapping_id IS NOT NULL AND order_id = ".$order['id'])){
			$sql_remove = "DELETE FROM bs_mapping_silver_orders WHERE order_id =".$map_item[0];
			echo $sql_remove;
			echo "\n";
		}
	}
	
	$sql = "SELECT DISTINCT(purchase_id) FROM bs_mapping_silver_purchases";
	$rst = $dbc->Query($sql);
	while($map_item = $dbc->Fetch($rst)){
		$purchase = $dbc->GetRecord("bs_purchase_spot","*","id=".$map_item[0]);
		$item = $dbc->GetRecord("bs_mapping_silver_purchases","SUM(amount)","purchase_id=".$map_item[0]);
		if($purchase['amount'] != $item[0]){
			$remain = $purchase['amount'] - $item[0];
			$sql_new = "INSERT INTO bs_mapping_silver_purchases VALUES(DEFAULT,NULL,".$purchase['id'].",".$remain.");";
			echo $sql_new;
			echo "\n";
		}
		
		if(!$dbc->HasRecord("bs_mapping_silver_purchases","mapping_id IS NOT NULL AND purchase_id = ".$purchase['id'])){
			$sql_remove = "DELETE FROM bs_mapping_silver_purchases WHERE purchase_id=".$map_item[0];
			echo $sql_remove;
			echo "\n";
		}
		
	}
	
	
	
	



	
	

	$dbc->Close();
?>
