<?php
session_start();
include_once "../../../../config/define.php";
@ini_set('display_errors', 1);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";
include_once "../../../../include/iface.php";
include_once "../../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$product = $dbc->GetRecord("bs_products", "*", "id=" . $_POST['id']);
$modal = new imodal($dbc, $os->auth);
//$modal->setParam($_POST);
$modal->setModel("dialog_edit_product", "Edit Product");
$modal->initiForm("form_editproduct", "fn.app.database.company.product.edit()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.database.company.product.edit()")
));
$modal->SetVariable(array(
	array("id", $product['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "code",
			"caption" => "Code",
			"placeholder" => "Product Code",
			"value" => $product['code']
		)
	),
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Product Name",
			"value" => $product['name']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "detail",
			"caption" => "Detail",
			"placeholder" => "Detail",
			"value" => $product['detail']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
