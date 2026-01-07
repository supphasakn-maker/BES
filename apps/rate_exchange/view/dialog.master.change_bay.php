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
$bay_rate = $os->load_variable("bay_rate");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_change_bay_master", "Change BAY Rate");
$modal->initiForm("form_change_baymaster");

$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Change", "fn.app.rate_exchange.master.change_bay()")
));

$blueprint = array(
    array(
        array(
            "name" => "rate",
            "caption" => "RATE",
            "placeholder" => "BAY RATE",
            "value" => $bay_rate
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
