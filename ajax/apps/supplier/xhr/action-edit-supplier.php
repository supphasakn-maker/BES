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

	if($dbc->HasRecord("bs_suppliers","name = '".$_POST['name']."' AND id !=".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Supplier Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'#updated' => 'NOW()',
			'comment' => addslashes($_POST['comment']),
			'#type' => $_POST['type'],
			'#gid' => $_POST['gid']
		);

		if($dbc->Update("bs_suppliers",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$supplier = $dbc->GetRecord("bs_suppliers","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"supplier-edit",$_POST['id'],array("bs_suppliers" => $supplier));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
