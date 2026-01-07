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

	
	$data = array(
		'remove_reason' => $_POST['remove_reason'],
		'#updated' => 'NOW()',
		'#status' => -1,
	);

	if($dbc->Update("bs_orders",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
		$order = $dbc->GetRecord("bs_orders","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"order-remove_each",$_POST['id'],array("orders" => $order));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Change"
		));
	}
	

	$dbc->Close();
?>
