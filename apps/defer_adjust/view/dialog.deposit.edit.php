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
$deposit = $dbc->GetRecord("bs_match_deposit", "*", "id=" . $_POST['id']);


$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_deposit", "EDIT DEPOSIT");
$modal->initiForm("form_editdeposit");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.defer_adjust.deposit.edit()")
));
$modal->SetVariable(array(
    array("id", $deposit['id'])
));



$blueprint = array(
    array(
        array(
            "name" => "supplier_id",
            "caption" => "Supplier",
            "type" => "comboboxdb",
            "source" => array(
                "table" => "bs_suppliers",
                "value" => "id",
                "name" => "name",
                "where" => "status = 1"
            ),
            "value" => $deposit['supplier_id']
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "flex" => 4,
            "value" => $deposit['date']
        )
    ),
    array(
        array(
            "name" => "usd",
            "caption" => "USD",
            "placeholder" => "Total USD Deposit",
            "value" => $deposit['usd']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
