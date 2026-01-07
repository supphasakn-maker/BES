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
$contract = $dbc->GetRecord("bs_transfers", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_contract_product", "Edit Product");
$modal->initiForm("form_editproductcontract");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.forward_contract.contract.edit_product()")
));
$modal->SetVariable(array(
    array("id", $contract['id'])
));

$blueprint = array(
    array(
        array(
            "name" => "product_id",
            "caption" => "PRODUCT",
            "type" => "comboboxdb",
            "source" => array(
                "table" => "bs_products",
                "value" => "id",
                "name" => "name"
            ),
            "value" => $contract['product_id']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
