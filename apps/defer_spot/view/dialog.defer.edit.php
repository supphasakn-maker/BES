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
$defer = $dbc->GetRecord("bs_defer_spot", "*", "id=" . $_POST['id']);


$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_defer", "EDIT ADJUST DEFER");
$modal->initiForm("form_editdefer");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.defer_spot.defer.edit()")
));
$modal->SetVariable(array(
    array("id", $defer['id'])
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
            "value" => $defer['supplier_id']
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "value_date",
            "caption" => "Date",
            "flex" => 4,
            "value" => $defer['value_date']
        )
    ),
    array(
        array(
            "name" => "rate_spot",
            "caption" => "SPOT",
            "placeholder" => "SPOT",
            "value" => $defer['rate_spot']
        ),
    ),
    array(
        array(
            "name" => "rate_pmdc",
            "caption" => "PM/DC",
            "placeholder" => "PM/DC",
            "value" => $defer['rate_pmdc']
        ),
    ),
    array(
        array(
            "name" => "amount",
            "caption" => "AMOUNT",
            "placeholder" => "Total adjust in THB",
            "value" => $defer['amount']
        )
    ),
    array(
        array(
            "name" => "price",
            "caption" => "PRICE",
            "placeholder" => "PRICE",
            "value" => $defer['price']
        )
    ),
    array(
        array(
            "name" => "ref",
            "caption" => "REF",
            "placeholder" => "REF",
            "value" => $defer['ref']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
