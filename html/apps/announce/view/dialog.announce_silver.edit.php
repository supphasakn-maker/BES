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
$announce = $dbc->GetRecord("bs_announce_silver", "*", "id=" . $_POST['id']);

$readonly = false;
if (strtotime($announce['created']) > strtotime(date("Y-m-d"))) {
	$readonly = true;
}



$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_announce_silver", "EDIT PRICE SILVER");
$modal->initiForm("form_editannounce");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.announce.announce_silver.edit()")
));
$modal->SetVariable(array(
	array("id", $announce['id'])
));



$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"flex" => 4,
			"value" => $announce['date']
		)
	),
	array(
		array(
			"name" => "no",
			"caption" => "ครั้งที่",
			"placeholder" => "ครั้งที่",
			"value" => $announce['no']
		)
	),
	array(
		array(
			"name" => "rate_spot",
			"caption" => "Rate Spot",
			"placeholder" => "Rate Spot",
			"value" => number_format($announce['rate_spot'], 2)
		)
	),
	array(
		array(
			"name" => "rate_exchange",
			"caption" => "Rate Exchange",
			"placeholder" => "Rate Exchange",
			"value" => number_format($announce['rate_exchange'], 2)
		)
	),
	array(
		array(
			"name" => "rate_pmdc",
			"caption" => "Rate PM/DC",
			"placeholder" => "Rate PM/DC",
			"value" => number_format($announce['rate_pmdc'], 2)
		)
	),
	array(
		array(
			"name" => "buy",
			"caption" => "ราคาขายออก",
			"placeholder" => "ราคาขายออก",
			"value" => number_format($announce['buy'], 2)
		)
	),
	array(
		array(
			"name" => "sell",
			"caption" => "ราคารับซื้อ",
			"placeholder" => "ราคารับซื้อ",
			"value" => number_format($announce['sell'], 2)
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
