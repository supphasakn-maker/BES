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

	$round_id = $dbc->GetRecord("bs_productions_round","*","id=".$_POST['round_id']);


	echo json_encode(array(
		'round_id'=>$round_id,
        'import_lot'=>$round_id,
		'amount_balance'=>$round_id,
		'product_type_id'=>$round_id,
		'factory'=>$round_id,

	));

	$dbc->Close();
?>
