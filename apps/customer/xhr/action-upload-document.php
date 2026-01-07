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
	
	$os->set_upload_allow("png,jpg,pdf");
	
	$customer = $dbc->GetRecord("bs_customers","*","id=".$_POST['id']);
	if(is_null($customer['imgs'])){
		$imgs = array();
	}else{
		$imgs = json_decode($customer['imgs'],true);
	}
	
	$aFail = array();
	
	
	if($_FILES['file']['name'][0]==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please upload photo"
		));
	}else{
		$iNumber = $os->load_variable("iCustomerDocument");
		
		
		for($i=0;$i<count($_FILES['file']['name']);$i++){
			$iNumber++;
			$filename = $_FILES['file']['name'][$i];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$path = 'binary/customer/'.$iNumber.".".$ext;
			
			try{
				$file = array(
					"name" => $_FILES['file']['name'][$i],
					"type" => $_FILES['file']['type'][$i],
					"tmp_name" => $_FILES['file']['tmp_name'][$i],
					"error" => $_FILES['file']['error'][$i],
					"size" => $_FILES['file']['size'][$i]
				);
				
				$uploaded = $os->upload($file,"../../../".$path);
			
				if($uploaded['success']){
					array_push($imgs,$path);
				}else{
					array_push($aFail,$uploaded['msg']);
				}
			} catch (Exception $e) {
				
			}
		}
		
		$dbc->update("bs_customers",array("imgs"=>json_encode($imgs)),"id=".$_POST['id']);
		$os->save_variable('iCustomerDocument',$iNumber);
		
		echo json_encode(array(
			'success'=>true,
			'imgs' => $imgs,
			'faile' => $aFail
		));
	}

	$dbc->Close();
?>