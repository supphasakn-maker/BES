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
			$id = isset($this->param['id'])?$this->param['id']:array();
			echo '<div>Are you sure to set primary address ?</div>';
		
				echo '<ul>';
					$address = $dbc->GetRecord("os_address","*","id=".$id);
					echo '<li>'.$address['id'].' : '.$address['address'].'</li>';
			
				echo '</ul>';
			
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_primary_address","Set Primary Address");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-success","Set Primary","fn.app.contact.address.primary(".$_POST['id'].")")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>