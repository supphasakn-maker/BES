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
			$noti = $dbc->GetRecord("os_notifications","*","id=".$this->param['id']);
			
			//$sender = $os->getAuthInfo($noti['source']);
			
			switch($noti['type']){
				case "alert":
					$icon = "fa-bell";
					$color = "bg-color-red";
					break;
				case "schedule":
					$icon = "fa-calendar";
					$color = "bg-color-greenLight";
					break;
				case "notify":
					$icon = "fa-bullhorn";
					$color = "bg-color-blue";
					break;
			}
			
			echo '<div class="media">';
					echo '<div class="media-left">';
						echo '<a href="#">';
							echo '<em class="badge padding-5 no-border-radius ' .$color.' pull-left margin-right-5">';
								echo '<i class="fa '.$icon.' fa-fw fa-2x"></i>';
							echo '</em>';
						echo '</a>';
					echo '</div>';
					echo '<div class="media-body">';
						echo '<h4 class="media-heading">';
							echo $noti['topic'];
						echo '</h4>';
						echo $noti['detail'];
					echo '</div>';
				echo '</div>';
			
			if(is_null($noti['acknowledge'])){
				$dbc->Update("os_notifications",array("#acknowledge"=>"NOW()"),"id=".$noti['id']);
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