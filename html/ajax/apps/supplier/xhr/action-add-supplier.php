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


	if($dbc->HasRecord("bs_suppliers","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Supplier Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'comment' => addslashes($_POST['comment']),
			'#type' => $_POST['type'],
			'#gid' => $_POST['gid']
		);

		if($dbc->Insert("bs_suppliers",$data)){
			$supplier_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $supplier_id
			));

			$supplier = $dbc->GetRecord("bs_suppliers","*","id=".$supplier_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"supplier-add",$supplier_id,array("bs_suppliers" => $supplier));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
