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
	
	$unlink = "";
	$action = array();
	
	switch($_POST['type']){
		case "import":
			$import = $dbc->GetRecord("bs_imports","*","id=".$_POST['id']);
			$iVariable = "iImport";
			$path_begin = 'binary/import/';
			if($import['info_coa_files']!="")$unlink = "../../../".$import['info_coa_files'];
			break;
		case "bank":
			$bank = $dbc->GetRecord("bs_banks","*","id=".$_POST['id']);
			$iVariable = "iIcon";
			$path_begin = 'binary/icon/';
			if($bank['icon']!="")$unlink = "../../../".$bank['icon'];
			break;
		case "contact":
			$contact = $dbc->GetRecord("os_contacts","*","id=".$_POST['id']);
			$iVariable = "iAvatarNumber";
			$path_begin = 'binary/contact/';
			if($contact['avatar']!="")$unlink = "../../../".$contact['avatar'];
			break;
		case "customer":
			$customer = $dbc->GetRecord("customers","*","id=".$_POST['id']);
			if($customer['type']=="both"){
				$organization = $dbc->GetRecord("organizations","*","id=".$customer['org_id']);
				$iVariable = "iOrgNumber";
				$path_begin = 'binary/organization/';
				if($organization['logo']!="")$unlink = "../../../".$organization['logo'];
			}else if($customer['type']=="organization"){
				$organization = $dbc->GetRecord("organizations","*","id=".$customer['org_id']);
				$iVariable = "iOrgNumber";
				$path_begin = 'binary/organization/';
				if($organization['logo']!="")$unlink = "../../../".$organization['logo'];
			}else{
				$contact = $dbc->GetRecord("os_contacts","*","id=".$customer['contact']);
				$iVariable = "iAvatarNumber";
				$path_begin = 'binary/contact/';
				if($contact['avatar']!="")$unlink = "../../../".$contact['avatar'];
			}
			break;
		case "profile":
			$user = $dbc->GetRecord("os_users","*","id=".$_POST['id']);
			$contact = $dbc->GetRecord("os_contacts","*","id=".$user['contact']);
			$iVariable = "iAvatarNumber";
			$path_begin = 'binary/contact/';
			if($contact['avatar']!="")$unlink = "../../../".$contact['avatar'];
			break;
		case "brand":
			$brand = $dbc->GetRecord("brands","*","id=".$_POST['id']);
			$iVariable = "iBrand";
			$path_begin = 'binary/brand/';
			if($brand['img']!="")$unlink = "../../../".$brand['img'];
			break;
		case "category":
			$category = $dbc->GetRecord("categories","*","id=".$_POST['id']);
			$iVariable = "iCategory";
			$path_begin = 'binary/category/';
			if($category['img']!="")$unlink = "../../../".$category['img'];
			break;
	}
	
	
	if($_FILES['file']['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please upload photo"
		));
	}else{
		$iNumber = $os->load_variable($iVariable);
		$iNumber++;
		
		$filename = $_FILES['file']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$path = $path_begin.$iNumber.".".$ext;
		
		try{
			$uploaded = $os->upload($_FILES['file'],"../../../".$path);
			if(!$uploaded['success']){
				echo json_encode(array(
					'success'=>false,
					'msg' => $uploaded['msg']
				));
			}else{
				$os->save_variable($iVariable,$iNumber);
				if($unlink!="")if(file_exists($unlink))unlink($unlink);
				
				switch($_POST['type']){
					case "import":
						array_push($action,array("retable","#tblImport"));
						array_push($action,array("rephoto","#preview_photo"));
						$dbc->Update("bs_imports",array("info_coa_files" => $path),'id='.$_POST['id']);
						break;
					case "bank":
						array_push($action,array("retable","#tblDatabase"));
						array_push($action,array("rephoto","#preview_photo"));
						$dbc->Update("bs_banks",array("icon" => $path),'id='.$_POST['id']);
						break;
					case "contact":
						array_push($action,array("retable","#tblContact"));
						array_push($action,array("retable","#tblUser"));
						array_push($action,array("rephoto","#preview_photo"));
						$dbc->Update("os_contacts",array("avatar" => $path),'id='.$_POST['id']);
						break;
					case "customer":
						array_push($action,array("retable","#tblCustomer"));
						array_push($action,array("rephoto","#preview_photo"));
						if($customer['type']=="both"){
							$dbc->Update("organizations",array("logo" => $path),'id='.$customer['org_id']);
						}else if($customer['type']=="organization"){
							$dbc->Update("organizations",array("logo" => $path),'id='.$customer['org_id']);
						}else{
							$dbc->Update("os_contacts",array("avatar" => $path),'id='.$customer['contact']);
						}
						break;
					case "profile":
						array_push($action,array("reload","body"));
						$dbc->Update("os_contacts",array("avatar" => $path),'id='.$_POST['id']);
						break;
					case "brand":
						array_push($action,array("reload","body"));
						$dbc->Update("brands",array("img" => $path),'id='.$_POST['id']);
						break;
					case "category":
						array_push($action,array("reload","body"));
						$dbc->Update("categories",array("img" => $path),'id='.$_POST['id']);
						break;
				}
				
				echo json_encode(array(
					'success'=>true,
					'path' => "$path",
					'action' => $action
				));
			}
		} catch (Exception $e) {
			echo json_encode(array(
				'success'=>false,
				'msg' => $e
			));
		}
		
	}

	$dbc->Close();
?>