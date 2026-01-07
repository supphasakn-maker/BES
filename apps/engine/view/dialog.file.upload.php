<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			$type = $this->param['type'];
			$path = "img/default/noimage.png";
			
			$form = new iform($this->dbc,$this->auth);
			$form->setFrom("form_uploader");
			$form->setOption("enctype","multipart/form-data");
			$form->setOption("class","d-none");
			$form->SetVariable(array(
				array("id",$this->param['id']),
				array("type",$this->param['type'])
			));
			$form->SetBlueprint(array(
				array(
					array(
						"type" => "file",
						"name" => "file"
					)
				)
			));
			$form->EchoInterface();
			
			switch($type){
				case "import":
					$import = $dbc->GetRecord("bs_imports","*","id=".$this->param['id']);
					if(!is_null($import['info_coa_files']) && $import['info_coa_files'] != ""){
						if(file_exists("../../../".$import['info_coa_files'])){
							$path = $import['info_coa_files'];
						}
					}
					break;
				case "contact":
					$contact = $dbc->GetRecord("os_contacts","*","id=".$this->param['id']);
					if(!is_null($contact['avatar']) && $contact['avatar'] != ""){
						if(file_exists("../../../".$contact['avatar'])){
							$path = $contact['avatar'];
						}
					}
					break;
				
				case "customer":
					$customer = $dbc->GetRecord("customers","*","id=".$this->param['id']);
					if($customer['type']=="both"){
						$organization = $dbc->GetRecord("organizations","*","id=".$customer['org_id']);
						$avatar = is_null($organization['logo'])?null:$organization['logo'];
					}else if($customer['type']=="organization"){
						$organization = $dbc->GetRecord("organizations","*","id=".$customer['org_id']);
						$avatar = is_null($organization['logo'])?null:$organization['logo'];
					}else{
						$contact = $dbc->GetRecord("os_contacts","*","id=".$customer['contact']);
						$avatar = $contact['avatar'];
					}
					
					if(!is_null($avatar) && $avatar != ""){
						if(file_exists("../../../".$avatar)){
							$path = $avatar;
						}
					}
					break;
				case "profile":
					$user = $dbc->GetRecord("os_users","*","id=".$this->param['id']);
					$contact = $dbc->GetRecord("os_contacts","*","id=".$user['contact']);
					if(!is_null($contact['avatar']) && $contact['avatar'] != ""){
						if(file_exists("../../../".$contact['avatar'])){
							$path = $contact['avatar'];
						}
					}
					break;
				case "brand":
					$brand = $dbc->GetRecord("brands","*","id=".$this->param['id']);
					if(!is_null($brand['img']) && $brand['img'] != ""){
						if(file_exists("../../../".$brand['img'])){
							$path = $brand['img'];
						}
					}
					break;
				case "category":
					$brand = $dbc->GetRecord("categories","*","id=".$this->param['id']);
					if(!is_null($brand['img']) && $brand['img'] != ""){
						if(file_exists("../../../".$brand['img'])){
							$path = $brand['img'];
						}
					}
					break;
				case "bank":
					$bank = $dbc->GetRecord("bs_banks","*","id=".$this->param['id']);
					if(!is_null($bank['icon']) && $bank['icon'] != ""){
						if(file_exists("../../../".$bank['icon'])){
							$path = $bank['icon'];
						}
					}
					break;
				
			}
			
			echo '<div class="text-center">';
				echo '<img id="preview_photo" class="img-fluid"  src="'.$path.'">';
			echo '</div>';
			
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_file_upload","File Uploader");
	$modal->setButton(array(
		array("action","btn-danger float-sm-left","Remove","fn.app.engine.file.clear()"),
		array("action","btn-warning float-sm-left","Change","fn.app.engine.file.upload()"),
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();
	$dbc->Close();
?>