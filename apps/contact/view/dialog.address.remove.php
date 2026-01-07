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
			if(is_array($this->param['item'])){
				$items = isset($this->param['item'])?$this->param['item']:array();
				$removable = true;
				
				if(count($items)==0){
					$removable = false;
				}
				
				if($removable){
					echo '<ul>';
					foreach($items as $item){
						$contact = $dbc->GetRecord("os_address","*","id=".$item);
						echo '<li>'.$contact['id'].' : '.$contact['address'].'</li>';
					}
					echo '</ul>';
				}else{
					echo 'No item will be removed!';
				}
			}else{
				$contact = $dbc->GetRecord("os_address","*","id=".$this->param['item']);
				echo '<li>'.$contact['id'].' : '.$contact['address'].'</li>';
			}
		}
	}
	
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_remove_address","Address Contact");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Remove","fn.app.contact.address.remove(".(is_array($_POST['item'])?"":$_POST['item']).")")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>