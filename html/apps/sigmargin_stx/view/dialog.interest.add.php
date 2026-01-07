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
$modal->setModel("dialog_add_interest", "Add Interest");
$modal->initiForm("form_addinterest");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.sigmargin_stx.interest.add()")
));

$blueprint = array(
    array(
        array(
            "type" => "date",
            "name" => "date_start",
            "caption" => "Date Form",
            "value" => date("Y-m-d"),
            "flex" => 4
        ),
        array(
            "type" => "date",
            "name" => "date_end",
            "caption" => "Date to",
            "value" => date("Y-m-d"),
            "flex" => 4
        )
    ),
    array(
        array(
            "type" => "number",
            "name" => "interest",
            "caption" => "interest",
            "placeholder" => "interest"
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
