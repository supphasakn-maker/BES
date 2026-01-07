<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$order_condition = " bs_orders.parent IS NULL AND bs_orders.status > -1";

$balance = -4710.36;
if ($_POST['type'] == "daily") {
	include "overview/load-daily.php";
} else if ($_POST['type'] == "monthly") {
	include "overview/load-monthly.php";
} else if ($_POST['type'] == "yearly") {
	include "overview/load-yearly.php";
} else {
	include "overview/load-default.php";
}

$dbc->Close();
