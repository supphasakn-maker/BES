<?php
	session_start();
	include_once "../config/define.php";
	include_once "../include/db.php";
	include_once "../include/oceanos.php";
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
	$os = new oceanos($dbc);
	
	
	$sql = "SELECT * FROM messages WHERE destination=".$_SESSION['auth']['user_id'];
	$rst = $dbc->Query($sql);
	
	
	echo '<ul class="notification-body">';
	while($noti = $dbc->Fetch($rst)){
		
		$sender = $os->getAuthInfo($noti['source']);
	
		echo '<li onclick="fn.oceanos.message.view('.$noti['id'].',this)">';
			echo '<span class="'.(is_null($noti['opened'])?" unread":"").'">';
				echo '<a href="javascript:void(0);" class="msg">';
					echo '<img src="'.$sender['avatar'].'" alt="" class="air air-top-left margin-top-5" width="40" height="40" />';
					echo '<span class="from">'.$sender['display'].' <i class="icon-paperclip"></i></span>';
					echo '<time>'.time_elapsed_string($noti['created']).'</time>';
					echo '<span class="subject">'.$noti['msg'].'</span>';
				echo '</a>';
			echo '</span>';
		echo '</li>';
	}
	$dbc->Close();
	echo '</li>';
	
?>