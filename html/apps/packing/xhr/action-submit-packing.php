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
		'#status' => "1",
		'#updated' => 'NOW()'
	);

	if($dbc->Update("bs_packings",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
		
		$dbc->Update("bs_packing_items",array("#status"=>"1"),"packing_id=".$_POST['id']);
		
		
		$packing = $dbc->GetRecord("bs_packings","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"packing-submit",$_POST['id'],array("packings" => $packing));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Change"
		));
	}
	

	$dbc->Close();
?>
