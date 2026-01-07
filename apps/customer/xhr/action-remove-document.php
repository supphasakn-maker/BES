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
	
	$customer = $dbc->GetRecord("bs_customers","*","id=".$_POST['id']);
	$imgs = json_decode($customer['imgs'],true);
	
	$imgs_new = array();
	for($i=0;$i<count($imgs);$i++){
		if($_POST['pos']==$i){
			if(file_exists("../../../".$imgs[$i]))unlink("../../../".$imgs[$i]);
		}else{
			array_push($imgs_new,$imgs[$i]);
		}
	}
	$dbc->update("bs_customers",array("imgs"=>json_encode($imgs_new)),"id=".$_POST['id']);
	
	echo json_encode(array(
		'success'=>true,
		'msg' => "Remove Complete",
		'imgs' => $imgs_new
	));

	$dbc->Close();
?>