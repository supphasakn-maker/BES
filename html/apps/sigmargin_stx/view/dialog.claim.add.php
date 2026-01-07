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
$modal->setModel("dialog_add_claim", "Add Claim");
$modal->initiForm("form_addclaim");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.sigmargin_stx.claim.add()")
));

$blueprint = array(
    array(
        array(
            "name" => "amount",
            "caption" => "กิโลซื้อ/ขาย",
            "placeholder" => "กิโลซื้อ/ขาย",
            "type" => "number"
        )
    ),
    array(
        array(
            "type" => "combobox",
            "name" => "purchase_type",
            "source" => array(
                array("Buy", "value" => "Buy", 'Buy'),
                array("Sell", "value" => "Sell", 'Sell')
            ),
            "caption" => "Buy/Sell"
        )
    ),
    array(
        array(
            "name" => "rate_spot",
            "caption" => "SPOT",
            "placeholder" => "SPOT",
            "type" => "number"
        )
    ),
    array(
        array(
            "name" => "rate_pmdc",
            "caption" => "Pm/Dc",
            "placeholder" => "Pm/Dc",
            "type" => "number"
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "value" => date("Y-m-d"),
            "flex" => 4
        )
    )

);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
