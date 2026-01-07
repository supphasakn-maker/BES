<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

foreach ($_POST['items'] as $item) {
	$import = $dbc->GetRecord("bs_imports", "*", "id=" . $item);

	$dbc->Delete("bs_imports", "id=" . $item);

	$dbc->Update("bs_reserve_silver", array(
		"#import_id" => "NULL"
	), "import_id = " . $item);

	$dbc->Update("bs_purchase_spot", array(
		"#status" => 1,
		"#import_id" => "NULL"
	), "import_id = " . $item);
	$dbc->Update("bs_purchase_spot_profit", array(
		"#status" => 1,
		"#import_id" => "NULL"
	), "import_id = " . $item);
	$os->save_log(0, $_SESSION['auth']['user_id'], "import-delete", $id, array("bs_imports" => $import));
}

$dbc->Close();
