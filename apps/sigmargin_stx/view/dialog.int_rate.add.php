<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_int_rate", "Add Int_rate");
$modal->initiForm("form_addint_rate");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.sigmargin_stx.int_rate.add()")
));

$blueprint = array(
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date"
        )
    ),
    array(
        array(
            "name" => "rate_short",
            "caption" => "Rate (-) Short",
            "placeholder" => "Rate (-) Short"
        )
    ),
    array(
        array(
            "name" => "rate",
            "caption" => "Rate (+) Long",
            "placeholder" => "Rate (+) Long"
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
