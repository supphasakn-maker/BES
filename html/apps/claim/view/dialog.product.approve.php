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
$claim = $dbc->GetRecord("bs_claims", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_approve_product", "Approve Claim");
$modal->initiForm("form_approveproduct");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "SAVE", "fn.app.claim.product.approve()")
));
$modal->SetVariable(array(
	array("id", $claim['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "solutions",
			"caption" => "วิธีการแก้ไขปัญหา",
			"type" => "textarea"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
