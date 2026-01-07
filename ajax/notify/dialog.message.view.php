<?php
	session_start();
	include_once "../../config/define.php";
	include_once "../../include/db.php";
	include_once "../../include/oceanos.php";
	include_once "../../include/iface.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	class myModal extends imodal{
		function body(){
			global $os;
			$dbc = $this->dbc;
			$noti = $dbc->GetRecord("os_messages","*","id=".$this->param['id']);
			$sender = $os->getAuthInfo($noti['source']);
			
			echo '<div class="media">';
				echo '<div class="media-left">';
					echo '<a href="#">';
						echo '<img src="'.$sender['avatar'].'" alt="" class="media-object" width="40" height="40" />';
					echo '</a>';
				echo '</div>';
				echo '<div class="media-body ml-2">';
					echo '<h4 class="media-heading">';
						echo $sender['display'];
					echo '</h4>';
					echo $noti['msg'];
				echo '</div>';
			echo '</div>';
			
			if(is_null($noti['opened'])){
				$dbc->Update("os_messages",array("#opened"=>"NOW()"),"id=".$noti['id']);
			}
		}
	}
	
	$modal = new myModal($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_view_message","Message");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>
