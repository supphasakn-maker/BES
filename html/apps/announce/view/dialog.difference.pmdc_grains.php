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
$pmdc_grains = $os->load_variable("pmdc_grains");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_pmdc_grains", "Change Premium/Discount Rate Grains");
$modal->initiForm("form_pmdc_grains");

$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Change", "fn.app.announce.difference.pmdc_grains()")
));

$blueprint = array(
    array(
        array(
            "name" => "pmdc_grains",
            "caption" => "Rate",
            "placeholder" => "Premium/Discount Rate Grains",
            "value" => $pmdc_grains
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
