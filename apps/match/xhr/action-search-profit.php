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

$balance = -4710.36;
if ($_POST['type'] == "daily") {
    include "profit/load-daily.php";
} else if ($_POST['type'] == "monthly") {
    include "profit/load-monthly.php";
} else if ($_POST['type'] == "yearly") {
    include "profit/load-yearly.php";
} else {
    include "profit/load-default.php";
}

$dbc->Close();
