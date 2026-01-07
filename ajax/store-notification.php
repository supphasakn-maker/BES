<?php
	session_start();
	include_once "../config/define.php";
	include_once "../include/db.php";
	include_once "../include/concurrent.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	
	function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	
	$dbc = new dbc;
	$dbc->Connect();
	$sql = "SELECT * FROM notifications WHERE user=".$_SESSION['auth']['user_id'];
	$rst = $dbc->Query($sql);
	
	
	
	
	echo '<ul class="notification-body">';
	while($noti = $dbc->Fetch($rst)){
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
		
		echo '<li onclick="fn.oceanos.notification.view('.$noti['id'].',this)">';
			echo '<span class="padding-10'.(is_null($noti['acknowledge'])?" unread":"").'">';
				echo '<em class="badge padding-5 no-border-radius ' .$color.' pull-left margin-right-5">';
					echo '<i class="fa '.$icon.' fa-fw fa-2x"></i>';
				echo '</em>';
				echo '<span>';
					echo $noti['topic'];
					echo ' <br>';
					echo '<span class="pull-right font-xs text-muted"><i>'.time_elapsed_string($noti['created']).'</i></span>';
				echo '</span>';
				
			echo '</span>';
		echo '</li>';
	}
	$dbc->Close();
	echo '</li>';
	
?>