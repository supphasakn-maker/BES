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


		$dbc->Delete("bs_productions_crucible","id=".$_POST['item']);

		$os->save_log(0,$_SESSION['auth']['user_id'],"productions_crucible-delete",$id,array("productions_crucible" => $spot));


	$dbc->Close();
?>
