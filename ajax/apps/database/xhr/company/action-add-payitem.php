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
	
	if($dbc->HasRecord("bs_payment_types","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'payitem Name is already exist.'
		));
	}else if($_POST['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert payitem name'
		));
	}else{
		$data = array(
			"#id" => "DEFAULT",
			"name" => $_POST['name'],
			"#negative" => $_POST['negative'],
		);
		
		if($dbc->Insert("bs_payment_types",$data)){
			$payitem_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $payitem_id
			));
			
			$payitem = $dbc->GetRecord("bs_payment_types","*","id=".$payitem_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"payitem-add",$payitem_id,array("bs_payment_types" => $payitem));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>