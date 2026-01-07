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
$purchase = $dbc->GetRecord("bs_purchase_spot", "ROUND(amount*(rate_pmdc+rate_spot)*32.1507,2)", "id=" . $_POST['id']);
//$purchase = $dbc->GetRecord("bs_purchase_spot","SUM(ROUND(amount*(rate_pmdc+rate_spot)*32.1507,2))","defer_id=".$defer['id']);

class imodal_view extends imodal
{
	function body()
	{
		//echo '<form name="uploader" class="d-none" enctype="multipart/form-data"><input type="file" name="file[]" multiple></form>';
	}
}

$modal = new imodal_view($dbc, $os->auth);

$modal->setModel("dialog_split_defer", "Split defer");
$modal->initiForm("form_splitdefer");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Split", "fn.app.defer_split.spot.split()")
));
$modal->SetVariable(array(
	array("id", $_POST['id'])
));


$blueprint = array(
	array(
		array(
			"name" => "amount",
			"caption" => "Total",
			"readonly" => true,
			"value" => $purchase[0],
			"flex" => 8
		),
		array(
			"type" => "button",
			"name" => "Append",
			"class" => "btn btn-warning",
			"onclick" => "fn.app.defer_split.spot.append_split()",
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
