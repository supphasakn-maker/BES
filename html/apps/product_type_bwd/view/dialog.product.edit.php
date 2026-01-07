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
$product = $dbc->GetRecord("bs_products_bwd", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_product", "Edit Product");
$modal->initiForm("form_editproduct");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.product_type_bwd.product.edit()")
));
$modal->SetVariable(array(
    array("id", $product['id'])
));

$blueprint = array(
    array(
        array(
            "name" => "code",
            "caption" => "Code",
            "placeholder" => "Code",
            "value" => $product['code']
        )
    ),
    array(
        array(
            "name" => "name",
            "caption" => "Name",
            "placeholder" => "Name",
            "value" => $product['name']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
