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
$modal->setModel("dialog_add_silver", "Add Silver");
$modal->initiForm("form_addsilver");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save", "fn.app.sigmargin_stx.silver.add()")
));

$columns = array(
    "id" => "bs_smg_stx_stx_trade .id",
    "date" => "bs_smg_stx_trade .date",
    "type" => "bs_smg_stx_trade .type",
    "purchase_type" => "bs_smg_stx_trade .purchase_type",
    "amount" => "bs_smg_stx_trade .amount",
    "rate_spot" => "bs_smg_stx_trade .rate_spot",
    "rate_pmdc" => "bs_smg_stx_trade .rate_pmdc",
);

$table = array(
    "index" => "id",
    "name" => "bs_smg_stx_trade ",
);

$blueprint = array(
    array(
        array(
            "type" => "combobox",
            "name" => "type",
            "source" => array(
                array("Physical", "value" => "Physical", 'Physical'),
                array("Trade", "value" => "Trade", 'Trade')
            ),
            "caption" => "Physical/Trade"
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
            "name" => "amount",
            "caption" => "Amount",
            "placeholder" => "amount",
            "type" => "number"
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
