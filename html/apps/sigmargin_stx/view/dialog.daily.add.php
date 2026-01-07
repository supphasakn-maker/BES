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
$modal->setModel("dialog_add_daily", "Add Daily");
$modal->initiForm("form_adddaily");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.sigmargin_stx.daily.add()")
));

$blueprint = array(
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "value" => date("Y-m-d")
        )
    ),
    array(
        array(
            "name" => "rollover",
            "caption" => "Rollover",
            "placeholder" => "Rollover"
        )
    ),
    array(
        array(
            "name" => "spot_sell",
            "caption" => "Spot Sell",
            "placeholder" => "Spot Sell"
        )
    ),
    array(
        array(
            "name" => "spot_buy",
            "caption" => "Spot Buy",
            "placeholder" => "Spot Buy"
        )
    ),
    array(
        array(
            "name" => "cash",
            "caption" => "Cash",
            "placeholder" => "Cash"
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
