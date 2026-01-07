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
$plan = $dbc->GetRecord("bs_incoming_plans", "*", "id=" . $_POST['id']);

class imodal_view extends imodal
{
	function body()
	{
		//echo '<form name="uploader" class="d-none" enctype="multipart/form-data"><input type="file" name="file[]" multiple></form>';
	}
}

$modal = new imodal_view($dbc, $os->auth);

$modal->setModel("dialog_split_plan", "Split Plan");
$modal->initiForm("form_splitplan");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Split", "fn.app.incomingplan.plan.split()")
));
$modal->SetVariable(array(
	array("id", $plan['id'])
));


$blueprint = array(
	array(
		array(
			"name" => "amount",
			"caption" => "Total",
			"readonly" => true,
			"value" => $plan['amount'],
			"flex" => 8
		),
		array(
			"type" => "button",
			"name" => "Append",
			"class" => "btn btn-warning",
			"onclick" => "fn.app.incomingplan.plan.append_split()",
			"flex" => 2
		)
	),
	array(
		"group" => "splited_zone",
		"items" => array()
	)

);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
