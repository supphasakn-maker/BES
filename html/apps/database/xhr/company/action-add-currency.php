<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	if($dbc->HasRecord("bs_currencies","code = '".$_POST['code']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'currency Code is already exist.'
		));
	}else if($_POST['code']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert currency code'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'code' => $_POST['code'],
			'name' => $_POST['name'],
			'value' => $_POST['value'],
			'#created' => "NOW()",
			'#updated' => "NOW()"
		);
		
		if($dbc->Insert("bs_currencies",$data)){
			$currency_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $currency_id
			));
			
			$currency = $dbc->GetRecord("bs_currencies","*","id=".$currency_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"currency-add",$currency_id,array("bs_currencies" => $currency));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>