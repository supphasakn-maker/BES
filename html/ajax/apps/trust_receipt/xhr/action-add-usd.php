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

	$transfer = $dbc->GetRecord("bs_transfers","*","id=".$_POST['id']);
	$contract_id = $transfer['id'];

	$total_usd = 0;
	$select_usd = explode(",",$_POST['select_usd']);
	$select_amount = explode(",",$_POST['select_amount']);
	$select_bank_date = explode(",",$_POST['select_bank_date']);
	$select_premium_date = explode(",",$_POST['select_premium_date']);
	$select_contact_no = explode(",",$_POST['select_contact_no']);
	$select_premium = explode(",",$_POST['select_premium']);
	
	for($i=0;$i<count($select_usd);$i++){
		$usd = $dbc->GetRecord("bs_purchase_usd","*","id=".$select_usd[$i]);
		
		if($select_premium[$i]!=""){
			$unpaid = $usd['amount']*($usd['rate_exchange']+$select_premium[$i]);
		}else{
			$unpaid = $usd['amount']*$usd['rate_exchange'];
		}
		
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
		$total_usd += $usd['amount'];
	}
	
	$fixed_value = $transfer['fixed_value']+$total_usd;
	$nonfixed_value = $transfer['nonfixed_value']-$total_usd;
	
	$data = array(
		"#fixed_value" => $fixed_value,
		"#nonfixed_value" => $nonfixed_value,
		'#updated' => 'NOW()'
	);

	if($dbc->Update("bs_transfers",$data,"id=".$contract_id)){
		$contract = $dbc->GetRecord("bs_transfers","*","id=".$contract_id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"contract-append-usd",$contract_id,array("contracts" => $contract));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Insert Error"
		));
	}
	

	$dbc->Close();
?>
