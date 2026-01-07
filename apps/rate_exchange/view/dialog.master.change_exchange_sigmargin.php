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
$rate_exchange_sigmargin = $os->load_variable("rate_exchange_sigmargin");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_change_exchange_sigmargin", "Change Rate Sigmargin");
$modal->initiForm("form_change_sigmargin");

$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Change", "fn.app.rate_exchange.master.change_exchange_sigmargin()")
));

$blueprint = array(
    array(
        array(
            "name" => "rate",
            "caption" => "RATE",
            "placeholder" => "SIGMARGIN RATE",
            "value" => $rate_exchange_sigmargin
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
