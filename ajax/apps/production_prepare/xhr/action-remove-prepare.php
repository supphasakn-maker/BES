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
	$item = $_POST['id'];
	
	$prepare = $dbc->GetRecord("bs_productions","*","id=".$item);
	$dbc->Delete("bs_productions","id=".$item);
	$dbc->Delete("bs_packing_items","production_id=".$item);
	$os->save_log(0,$_SESSION['auth']['user_id'],"prepare-delete",$id,array("prepares" => $prepare));
	

	$dbc->Close();
?>
