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
	$transfer_id = $_POST['id'];
	
	$data = array(
		"#value_usd_fixed" => $_POST['value_usd_fixed'],
		"#value_usd_nonfixed" => $_POST['value_usd_nonfixed'],
		"#rate_counter" => $_POST['rate_counter'],
		"#value_thb_fixed" => $_POST['value_thb_fixed'],
		"#value_thb_premium" => $_POST['value_thb_premium'],
		"#value_thb_net" => $_POST['value_thb_net'],
		"#updated" => 'NOW()'
	);
	

	if($dbc->Update("bs_transfers",$data,"id=".$transfer_id)){
		
		if(isset($_POST['usd_id']))
		for($i=0;$i<count($_POST['usd_id']);$i++){
			
			$data = array(
				"#purchase_id" => $_POST['usd_id'][$i],
				"#transfer_id" => $transfer_id,
				"#premium_type" => $_POST['premium_type'],
				"date" => $_POST['date_transfer'],
				"#premium_day" => $_POST['usd_premium_day'][$i]!=""?$_POST['usd_premium_day'][$i]:0,
				"#rate_premium" => $_POST['usd_rate_premium'][$i]!=""?$_POST['usd_rate_premium'][$i]:0,
				"#rate_counter" => $_POST['rate_counter'],
				"#premium" => $_POST['usd_premium'][$i],
				"fw_contract_no" => $_POST['usd_fw_contact_no'][$i]
			);
			if($_POST['usd_date_premium_start'][$i]==""){$data['#premium_start'] = "NULL";}else{$data['premium_start'] = $_POST['usd_date_premium_start'][$i];}
			if($_POST['usd_date_premium_end'][$i]==""){$data['#premium_end'] = "NULL";}else{$data['premium_end'] = $_POST['usd_date_premium_end'][$i];}
			
			$dbc->Insert("bs_transfer_usd ",$data);
			
		}
		
		
		$contract = $dbc->GetRecord("bs_transfers","*","id=".$transfer_id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"contract-append-usd",$transfer_id,array("contracts" => $contract));
		echo json_encode(array(
			'success'=>true
		));
	
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Insert Error"
		));
	}
	

	$dbc->Close();
?>
