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


	if($_POST['transfer_date']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input date'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			"bank" => $_POST['bank'],
			"transfer_date" => $_POST['transfer_date'],
			"type" => $_POST['method'],
			"#fixed_value" => $_POST['fixed_value'],
			"#nonfixed_value" => $_POST['nonfixed_value'],
			"#rate_counter" => $_POST['rate_counter'],
			"#total_transfer" => $_POST['total_transfer'],
			"#net_good_value" => $_POST['net_good_value'],
			"#deposit" => $_POST['deposit'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#supplier_id" => $_POST['supplier_id']
			
			
		);

		if($dbc->Insert("bs_transfers",$data)){
			$contract_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $contract_id
			));
			
			
			$select_purchase = explode(",",$_POST['select_purchase']);
			foreach($select_purchase as $purchase_id){
				$dbc->Update("bs_purchase_spot",array(
					"#transfer_id" => $contract_id
				),"id = ".$purchase_id);
			}
			
			$select_usd = explode(",",$_POST['select_usd']);
			$select_amount = explode(",",$_POST['select_amount']);
			$select_bank_date = explode(",",$_POST['select_bank_date']);
			$select_premium_date = explode(",",$_POST['select_premium_date']);
			$select_contact_no = explode(",",$_POST['select_contact_no']);
			$select_thbpremium = explode(",",$_POST['select_thbpremium']);
			$select_premium = explode(",",$_POST['select_premium']);
			
			
			if($_POST['select_usd']!="")
			for($i=0;$i<count($select_usd);$i++){
				$usd = $dbc->GetRecord("bs_purchase_usd","*","id=".$select_usd[$i]);
				
				$unpaid = $select_thbpremium[$i];
				/*
				if($select_premium[$i]!=""){
					$unpaid = $usd['amount']*($usd['rate_exchange']+$select_premium[$i]);
				}else{
					$unpaid = $usd['amount']*$usd['rate_exchange'];
				}
				*/
				
				
				
				$data = array(
					"#premium" => $select_premium[$i]!=""?$select_premium[$i]:"NULL",
					"#transfer_id" => $contract_id,
					"fw_contract_no" => $select_contact_no[$i],
					"#unpaid" => $unpaid,
				);
				
				if($select_bank_date[$i]==""){
					$data['#bank_date'] = "NULL";
				}else{
					$data['bank_date'] = $select_bank_date[$i];
				}
				if($select_premium_date[$i]==""){
					$data['#premium_start'] = "NULL";
				}else{
					$data['premium_start'] = $select_premium_date[$i];
					
				}
				
				$dbc->Update("bs_purchase_usd",$data,"id = ".$select_usd[$i]);
				
			}
			

			$contract = $dbc->GetRecord("bs_transfers","*","id=".$contract_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"contract-add",$contract_id,array("contracts" => $contract));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
