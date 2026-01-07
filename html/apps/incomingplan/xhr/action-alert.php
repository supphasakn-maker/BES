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

	$topic = "วางแผนของเข้า";
	$detail = 'พบข้อมูลใหม่ กดเพื่อเปลี่ยนหน้า <a href="#apps/incomingplan/index.php?view=show">ไปหน้า</a>';

	$sql="SELECT * FROM os_users";
	$rst = $dbc->Query($sql);
	while($user = $dbc->Fetch($rst)){
		$data = array(
			'#id' => "DEFAULT",
			'type' => "notify",
			'topic' => $topic,
			'detail' => addslashes($detail),
			'#user' => $user['id'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'#acknowledge' => 'NULL'
		);
		$dbc->Insert("os_notifications",$data);
		$id = $dbc->GetID();
		$notification = $dbc->GetRecord("os_notifications","*","id=".$id);
		$os->save_log(0,$_SESSION['auth']['user_id'],"notification-add",$id,array("os_notifications" => $notification));
	}


	$dbc->Close();
?>
