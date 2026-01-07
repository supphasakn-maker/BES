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
	
	if($dbc->HasRecord("os_organizations","name = '".$_POST['name']."' AND id != ".$_POST['org_id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'This name is already exist.'
		));
	}else{
		
		$data = array(
			'address' => $_POST['address'],
			'postal' => $_POST['postal'],
			'#updated' => "NOW()"
		);
		
		if(isset($_POST['country']))$data['#country']=$_POST['country'];
		if(isset($_POST['city']))$data['#city']=$_POST['city'];
		if(isset($_POST['subdistrict']))$data['#subdistrict']=$_POST['subdistrict'];
		if(isset($_POST['district']))$data['#district']=$_POST['district'];
		
		$dbc->Update("os_address", $data,"id=".$_POST['address_id']);
		$fulladdress = $os->load_fulladdress($_POST['address_id']);
		$dbc->Update("os_address",array("fulladdress"=>$fulladdress),"id=".$_POST['address_id']);
		
		$data = array(
			'name' => $_POST['name'],
			'email' => $_POST['email'],
			'phone' => $_POST['phone'],
			'fax' => $_POST['fax'],
			'website' => $_POST['website'],
			'#updated' => "NOW()",
			'type' => $_POST['type'],
			'tax_id' => $_POST['tax_id'],
			'branch' => $_POST['branch']
		);
		
		if($_POST['parent']=="0"){
			$data['#parent'] = "NULL";
		}else{
			$data['#parent'] = $_POST['parent'];
		}
		
		$dbc->Update("os_organizations", $data,"id=".$_POST['org_id']);
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> "Passed"
		));
		
		$organization = $dbc->GetRecord("os_organizations","*","id=".$_POST['org_id']);
		$address = $dbc->GetRecord("os_address","*","organization=".$_POST['org_id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"organization-edit",$organization['id'],array("organizations" => $organization,"address" => $address));
		
	}
	
	$dbc->Close();
?>