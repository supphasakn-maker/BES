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


	if($dbc->HasRecord("bs_customers","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Customer Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => addslashes($_POST['name']),
			"#gid" => $_POST['gid'],
			"contact" => $_POST['contact'],
			"phone" => $_POST['phone'],
			"fax" => $_POST['fax'],
			"email" => $_POST['email'],
			'shipping_address' => addslashes($_POST['shipping_address']),
			'billing_address' => addslashes($_POST['billing_address']),
			"remark" => addslashes($_POST['remark']),
			"comment" => addslashes($_POST['comment']),
			"#default_sales" => $_POST['default_sales'],
			"default_payment" => $_POST['default_payment'],
			"default_bank" => $_POST['default_bank'],
			"default_vat_type" => $_POST['default_vat_type'],
			"default_pack" => $_POST['default_pack'],
			'#imgs' => 'NULL',
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"org_name" => addslashes($_POST['org_name']),
			"org_taxid" => $_POST['org_taxid'],
			"org_branch" => $_POST['org_branch'],
			"org_address" => addslashes($_POST['billing_address'])
			
			
		);

		if($dbc->Insert("bs_customers",$data)){
			$customer_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $customer_id
			));

			$customer = $dbc->GetRecord("bs_customers","*","id=".$customer_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"customer-add",$customer_id,array("bs_customers" => $customer));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
