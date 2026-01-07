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
	
	$dd = date('Y-m-d');
    $time = date("H:i");


		$spot = $dbc->GetRecord("bs_announce_silver","*","id=".$_POST['item']);

		
		$dbc->Delete("bs_announce_silver","id=".$_POST['item']);

		$url        = 'https://notify-api.line.me/api/notify';
		$token      = 'w25DjV3UnXDI3VV4J6ThmrJqrb4J6NB4CGOn9k19w4B';
		$headers    = [
					'Content-Type: application/x-www-form-urlencoded',
					'Authorization: Bearer '.$token
				];
		$fields  =   'message=มีการลบประกาศ' ."\n".'วันที่: ' .$dd." / ".$time ."\n".
	
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url);
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec( $ch );
		curl_close( $ch );
	
		var_dump($result);
		$result = json_decode($result,TRUE);
		$os->save_log(0,$_SESSION['auth']['user_id'],"announce-silver-delete",$id,array("announce_silver" => $spot));


	$dbc->Close();
?>
