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
	$dd = date('Y-m-d');
    $time = date("H:i:s");
	
		$data = array(
			'#submitted' => 'NOW()',
			'#status' => 1
		);


		if($dbc->Update("bs_claims",$data,"id=".$_POST['id'])){

			$claim = $dbc->GetRecord("bs_claims","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"claim-submit",$_POST['id'],array("claim" => $claim));

			$url        = 'https://notify-api.line.me/api/notify';
			$token      = 'AjczYWnPSsTNcWiZfTn9fhKSTTa67cj72ihx9xfQc6f';
			$headers    = [
						'Content-Type: application/x-www-form-urlencoded',
						'Authorization: Bearer '.$token
					];
			$fields  =   'message=แจ้ง Claim' ."\n".'วันที่: ' .$dd." / ".$time ."\n".
			'Code: ' .$claim['code'] ."\n".
			'Sales: ' .$claim['contact_sales'] ."\n".
			'Type: ' .$claim['type'] ."\n".
			'ปัญหา: ' .$claim['issue'] ."\n".
			'รายละเอียด: ' .$claim['detail'] ."\n".
			'บริษัท: ' .$claim['org_name'] ."\n";
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($ch);
			curl_close($ch);
		
			var_dump($res);
			$json = json_decode($res,TRUE);
			echo json_encode(array(
				'success'=>true
			));

		}else{

			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
