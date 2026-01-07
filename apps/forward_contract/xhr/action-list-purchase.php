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
	
	$data = array();
	
	
	if($_POST['source']=="1"){
		$sql = "SELECT * FROM bs_purchase_spot
			WHERE supplier_id =".$_POST['supplier_id']." 
			AND status > -1 and type LIKE 'defer' and transfer_id IS NULL ORDER BY date DESC";
		$rst = $dbc->Query($sql);
		while($purchase = $dbc->Fetch($rst)){
			$supplier = $dbc->GetRecord("bs_suppliers","name","id=".$purchase['supplier_id']);
			$usd = $purchase['amount']*($purchase['rate_pmdc']+$purchase['rate_spot'])*32.1507;
			array_push($data,array(
				'id' => $purchase['id'],
				'date' => $purchase['date'],
				'amount' => $purchase['amount'],
				'supplier' => $supplier['name'],
				'usd' => $purchase['amount']*($purchase['rate_pmdc']+$purchase['rate_spot'])*32.1507,
				'formated_amount' => number_format($purchase['amount'],4),
				'formated_usd' => number_format($usd,4)
			));
		}
	}else if($_POST['source']=="2"){
		$sql = "SELECT * FROM bs_imports
			WHERE supplier_id =".$_POST['supplier_id']." 
			 and transfer_id IS NULL AND DATE(delivery_date) > '2022-07-05' ORDER BY created DESC";
		$rst = $dbc->Query($sql);
		while($import = $dbc->Fetch($rst)){
			$supplier = $dbc->GetRecord("bs_suppliers","name","id=".$import['supplier_id']);
			
			$sql = "SELECT
				SUM(amount) AS amount,
				SUM(ROUND(amount*(rate_pmdc+rate_spot)*32.1507,2)) AS usd
			FROM bs_purchase_spot WHERE import_id = ".$import['id'];
			$rst_spot = $dbc->Query($sql);
			$purchase = $dbc->Fetch($rst_spot);
			
			$usd = $purchase['usd'];
			if(!$dbc->HasRecord("bs_import_usd_splited","import_id=".$import['id']))
			array_push($data,array(
				'id' => $import['id'],
				'date' => $import['delivery_date'],
				'amount' => $purchase['amount'],
				'supplier' => $supplier['name'],
				'usd' => $usd,
				'formated_amount' => number_format($purchase['amount'],4),
				'formated_usd' => number_format($usd,2)
			));
		}
	}else if($_POST['source']=="3"){
		$sql = "SELECT * FROM bs_import_usd_splited WHERE transfer_id IS NULL ORDER BY created DESC";
		$rst = $dbc->Query($sql);
		while($combine = $dbc->Fetch($rst)){
			$value_ammount = 0;
			$value_usd = $combine['amount'];

			array_push($data,array(
				'id' => $combine['id'],
				'date' => $combine['created'],
				'supplier' => '-',
				'amount' => $value_ammount,
				'usd' => $value_usd,
				'formated_amount' => number_format($value_ammount,4),
				'formated_usd' => number_format($value_usd,2)
			));
		}
	}else if($_POST['source']=="4"){
		$sql = "SELECT * FROM bs_spot_usd_splited WHERE transfer_id IS NULL ORDER BY created DESC";
		$rst = $dbc->Query($sql);
		while($splitted = $dbc->Fetch($rst)){
			$value_ammount = 0;
			$value_usd = $splitted['amount'];

			array_push($data,array(
				'id' => $splitted['id'],
				'date' => $splitted['created'],
				'supplier' => '-',
				'amount' => $value_ammount,
				'usd' => $value_usd,
				'formated_amount' => number_format($value_ammount,4),
				'formated_usd' => number_format($value_usd,2)
			));
		}
	}
	
	
	
	echo json_encode($data);

	$dbc->Close();
?>