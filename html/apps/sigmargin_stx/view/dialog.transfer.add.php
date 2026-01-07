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
$modal->setModel("dialog_add_transfer", "Add Transfer");
$modal->initiForm("form_addtransfer");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.sigmargin_stx.transfer.add()")
));

$blueprint = array(
    array(
        array(
            "type" => "combobox",
            "name" => "type",
            "source" => array(
                array("ค่าสินค้า", "value" => "ค่าสินค้า", 'ค่าสินค้า'),
                array("โอนมัดจำ", "value" => "โอนมัดจำ", 'โอนมัดจำ')
            ),
            "caption" => "ค่าสินค้า/โอนมัดจำ"
        )
    ),
    array(
        array(
            "type" => "number",
            "name" => "amount_usd",
            "placeholder" => "USD",
            "caption" => "USD"
        )
    ),
    array(
        array(
            "type" => "number",
            "name" => "rate_pmdc",
            "placeholder" => "Pm / Dc",
            "caption" => "Pm / Dc"
        )
    ),
    array(
        array(
            "type" => "number",
            "name" => "amount_total",
            "placeholder" => "Total",
            "caption" => "Total"
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
