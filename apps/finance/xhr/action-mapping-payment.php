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
	
	$dbc->Delete("bs_payment_orders","payment_id = ".$_POST['id']);
	
	if(isset($_POST['order_id']))
	for($i=0;$i<count($_POST['order_id']);$i++){
		
		$data = array(
			"#id" => "DEFAULT",
			"#payment_id" => $_POST['id'],
			"#order_id" => $_POST['order_id'][$i],
			"#amount" => str_replace(",","",$_POST['paid'][$i])
		);
		
		$dbc->Insert("bs_payment_orders",$data);
	}
	
	$dbc->Delete("bs_payment_items","payment_id = ".$_POST['id']);
	if(isset($_POST['type_id']))
	for($i=0;$i<count($_POST['type_id']);$i++){
		
		$data = array(
			"#id" => "DEFAULT",
			"#type_id" => $_POST['type_id'][$i],
			"#payment_id" => $_POST['id'],
			"#amount" => $_POST['amount'][$i],
			"remark" => $_POST['remark'][$i]
		);
		
		$dbc->Insert("bs_payment_items",$data);
	}
	
	if(isset($_POST['deposit_id']))
	for($i=0;$i<count($_POST['deposit_id']);$i++){
		if($_POST['deposit_action'][$i]=="remove"){
			$dbc->Delete("bs_payment_deposits","id=".$_POST['deposit_id'][$i]);
			
		}else{
			if($_POST['deposit_id'][$i]==""){
				$data = array(
					"#id" => "DEFAULT",
					"#customer_id" => $_POST['customer_id'],
					"#payment_id" => $_POST['id'],
					"#amount" => $_POST['deposit'][$i],
					"#status" => 1
				);
				$dbc->Insert("bs_payment_deposits",$data);
			}else{
				$data = array(
					"#amount" => $_POST['deposit'][$i],
				);
				$dbc->Update("bs_payment_deposits",$data,"id=".$_POST['deposit_id'][$i]);
			}
		}
		
		
	}
	
	if(isset($_POST['deposit_use_id']))
	for($i=0;$i<count($_POST['deposit_use_id']);$i++){
		if($_POST['deposit_use_action'][$i]=="remove"){
			$dbc->Delete("bs_payment_deposit_use","id=".$_POST['deposit_use_id'][$i]);
		}else{
			if($_POST['deposit_use_id'][$i]==""){
				$data = array(
					"#id" => "DEFAULT",
					"#customer_id" => $_POST['customer_id'],
					"#payment_id" => $_POST['id'],
					"#amount" => $_POST['deposit_use'][$i],
					"#deposit_id" => $_POST['deposit_use_deposit_id'][$i]
				);
				$dbc->Insert("bs_payment_deposit_use",$data);
				
				$deposit = $dbc->GetRecord("bs_payment_deposits","amount","id=".$_POST['deposit_use_deposit_id'][$i]);
				$used = $dbc->GetRecord("bs_payment_deposit_use","SUM(amount)","deposit_id=".$_POST['deposit_use_deposit_id'][$i]);
				
				if($deposit['amount']==$used[0]){
					$dbc->Update("bs_payment_deposits",array("#status"=>0),"id=".$_POST['deposit_use_deposit_id'][$i]);
				}
				
			}else{
				$data = array(
					"#amount" => $_POST['deposit_use'][$i],
				);
				$dbc->Update("bs_payment_deposit_use",$data,"id=".$_POST['deposit_use_id'][$i]);
			}
		}
		
		
	}
	
	echo json_encode(array(
		'success'=>true
	));
			
	

	$dbc->Close();
?>
