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
$insure_15 = $os->load_variable("insure_15");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_insure_15", "Change Insurance Rate 15 Grams");
$modal->initiForm("form_insure_15");

$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Change", "fn.app.announce.difference.insure_15()")
));

$blueprint = array(
    array(
        array(
            "name" => "insure_15",
            "caption" => "Rate",
            "placeholder" => "Insurance Rate 15 Grams",
            "value" => $insure_15
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
