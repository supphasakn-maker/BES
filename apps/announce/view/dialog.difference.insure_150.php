<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$insure_150 = $os->load_variable("insure_150");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_insure_150", "Change Insurance Rate 150 Grams");
$modal->initiForm("form_insure_150");

$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Change", "fn.app.announce.difference.insure_150()")
));

$blueprint = array(
    array(
        array(
            "name" => "insure_150",
            "caption" => "Rate",
            "placeholder" => "Insurance Rate 150 Grams",
            "value" => $insure_150
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
