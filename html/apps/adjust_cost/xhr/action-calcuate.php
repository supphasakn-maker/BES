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


	$oz2kg = 32.1507;
	
		
		$sales = array();
		$purchase = array();
		$purchase_new = array();
		
		
		
		
		
		
		$total_sales_spot_value=0;
		$total_sales_spot_dicount=0;
		$total_sales_spot_net=0;
		
		$total_purchase_spot_value=0;
		$total_purchase_spot_dicount=0;
		$total_purchase_spot_net=0;
		
		$total_purchase_new_value=0;
		$total_purchase_new_dicount=0;
		$total_purchase_new_net=0;
		
		if(isset($_POST['sales']))
		foreach($_POST['sales'] as $sales_id){
			$sales_item = $dbc->GetRecord("bs_sales_spot","*","id=".$sales_id);
			array_push($sales,$sales_item);
			$total_sales_spot_value += $sales_item['amount']*$sales_item['rate_spot']*$oz2kg;
			$total_sales_spot_dicount += $sales_item['amount']*$sales_item['rate_pmdc']*$oz2kg;
			$total_sales_spot_net += $sales_item['amount']*($sales_item['rate_spot']+$sales_item['rate_pmdc'])*$oz2kg;
		}
		
		if(isset($_POST['purchase']))
		foreach($_POST['purchase'] as $purchase_id){
			$purchase_item = $dbc->GetRecord("bs_purchase_spot","*","id=".$purchase_id);
			array_push($purchase,$purchase_item);
			$total_purchase_spot_value += $purchase_item['amount']*$purchase_item['rate_spot']*$oz2kg;
			$total_purchase_spot_dicount += $purchase_item['amount']*$purchase_item['rate_pmdc']*$oz2kg;
			$total_purchase_spot_net += $purchase_item['amount']*($purchase_item['rate_spot']+$purchase_item['rate_pmdc'])*$oz2kg;
		}
		
		if(isset($_POST['purchase_new']))
		foreach($_POST['purchase_new'] as $purchase_id){
			$purchase_item = $dbc->GetRecord("bs_purchase_spot","*","id=".$purchase_id);
			array_push($purchase_new,$purchase_item);
			$total_purchase_new_value += $purchase_item['amount']*$purchase_item['rate_spot']*$oz2kg;
			$total_purchase_new_dicount += $purchase_item['amount']*$purchase_item['rate_pmdc']*$oz2kg;
			$total_purchase_new_net += $purchase_item['amount']*($purchase_item['rate_spot']+$purchase_item['rate_pmdc'])*$oz2kg;
		}
		
		echo json_encode(array(
		
			"sales_value" => $total_sales_spot_value,
			"sales_dicount" => $total_sales_spot_dicount,
			"sales_net" => $total_sales_spot_net,
			
			"purchase_value" => $total_purchase_spot_value,
			"purchase_discount" => $total_purchase_spot_dicount,
			"purchase_net" => $total_purchase_spot_net,
			
			"new_value" => $total_purchase_new_value,
			"new_discount" => $total_purchase_new_dicount,
			"new_net" => $total_purchase_new_net
		));

	$dbc->Close();
?>
