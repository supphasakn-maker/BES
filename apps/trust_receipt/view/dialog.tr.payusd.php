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
$transfer = $dbc->GetRecord("bs_transfers", "*", "id=" . $_POST['id']);
$principle = $transfer['value_usd_nonfixed'] - $transfer['paid_usd'];

$start_interest_date = $transfer['date'];
if ($dbc->HasRecord("bs_usd_payment", "transfer_id=" . $transfer['id'])) {
	$start_interest_date = $payment['interest_end'];
}



$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_payusd", "Paid Non Fixed");
$modal->initiForm("form_payusd");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.trust_receipt.tr.payusd()")
));
$modal->SetVariable(array(
	array("transfer_id", $transfer['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => date("Y-m-d")
		)
	),
	array(
		array(
			"name" => "principle",
			"caption" => "เงินคงเหลือ",
			"value" => $principle,
			"readonly" => true
		)
	),
	array(
		array(
			"name" => "rate_counter",
			"caption" => "Counter Rate",
			"value" => $transfer['rate_counter']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "interest_start",
			"caption" => "Start",
			"value" => $start_interest_date,
			"flex" => 4
		),
		array(
			"type" => "date",
			"name" => "interest_end",
			"caption" => "End",
			"value" => date("Y-m-d"),
			"flex" => 4
		)
	),
	array(
		array(
			"name" => "rate_interest",
			"caption" => "Interest Rate",
			"value" => 0,
			"flex" => 4
		),
		array(
			"name" => "interest_day",
			"caption" => "Day",
			"value" => floor((time() - strtotime($start_interest_date)) / 86400),
			"flex" => 4,
			"readonly" => true
		)
	),
	array(
		array(
			"name" => "interest",
			"caption" => "Interest",
			"value" => 0,
			"readonly" => true
		)
	),
	array(
		array(
			"name" => "paid",
			"caption" => "จ่ายจริง",
			"value" => 0
		)
	),
	array(
		array(
			"name" => "remain",
			"caption" => "Remain",
			"value" => 0,
			"readonly" => true
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
