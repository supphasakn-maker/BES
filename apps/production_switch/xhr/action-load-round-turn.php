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

$round_turn = $dbc->GetRecord("bs_productions_round", "*", "id=" . $_POST['round_turn']);


echo json_encode(array(

    'round_turn' => $round_turn,
    'import_lot' => $round_turn,
    'import_lot' => $round_turn,
    'amount_balance' => $round_turn,


));

$dbc->Close();
