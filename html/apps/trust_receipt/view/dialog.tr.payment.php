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
$html = '';
$paid_remain = $transfer['paid_thb'];

$html .= '<table class="table table-sm table-bordered">';
$html .= '<thead>';
$html .= '<tr>';
$html .= '<th class="text-center">Date</td>';
$html .= '<th class="text-center">Amount</td>';
$html .= '<th class="text-center">Paid</td>';
$html .= '<th class="text-center">Remain</td>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
$html .= '<tr>';
$html .= '<td class="text-center" colspan="3"></td>';
$html .= '<td class="text-center">' . number_format($paid_remain, 2) . '</td>';
$html .= '</tr>';
$sql = "SELECT  
			bs_transfer_usd.date AS date,
			SUM((bs_purchase_usd.amount*bs_purchase_usd.rate_finance)+bs_transfer_usd.premium) AS amount
		FROM bs_purchase_usd 
		LEFT JOIN bs_transfer_usd ON bs_purchase_usd.id =  bs_transfer_usd.purchase_id
		WHERE bs_transfer_usd.transfer_id = " . $transfer['id'] . "
		GROUP BY bs_transfer_usd.date
		ORDER BY bs_transfer_usd.date ASC
		";

$rst = $dbc->Query($sql);
while ($dailyusd = $dbc->Fetch($rst)) {
	if ($paid_remain >= $dailyusd['amount']) {
		$paid = $dailyusd['amount'];
	} else {
		$paid = $paid_remain;
	}
	$paid_remain -= $dailyusd['amount'];
	$html .= '<tr>';
	$html .= '<td class="text-center">' . $dailyusd['date'] . '</td>';
	$html .= '<td class="text-right">' . number_format($dailyusd['amount'], 2) . '</td>';
	$html .= '<td class="text-right">' . number_format($paid, 2) . '</td>';
	$html .= '<td class="text-right">' . number_format($paid_remain, 2) . '</td>';

	$html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';


$sql = "SELECT
			bs_transfer_usd.date AS date,
			SUM((bs_purchase_usd.amount*rate_finance)+bs_transfer_usd.premium) AS amount
		FROM bs_transfer_usd 
		LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id =  bs_transfer_usd.purchase_id
	
		WHERE bs_transfer_usd.transfer_id = " . $transfer['id'] . " 
		GROUP BY bs_transfer_usd.date
		ORDER BY bs_transfer_usd.date ASC
		";

$paid_remain = $transfer['paid_thb'];
$html .= $paid_remain;
$rst = $dbc->Query($sql);

$line = $dbc->Fetch($rst);
var_dump($paid_remain);

while ($paid_remain > 0) {
	if ($line['amount'] < $paid_remain) {
		$paid_remain = $paid_remain - $line['amount'];
	} else if ($line['amount'] >= $paid_remain) {
		$paid_remain = 0;
	}
	if (!$line = $dbc->Fetch($rst)) break;
}



echo $paid_remain;


$principle = $line['amount'] - $paid_remain;



//$principle = $transfer['value_thb_net']-$transfer['paid_thb'];
$start_interest_date = $transfer['date'];

//$payment = $dbc->GetRecord("bs_usd_payment","SUM(paid_thb)","bs_transfers=".$_POST['id'].'');


if ($dbc->HasRecord("bs_usd_payment", "transfer_id=" . $transfer['id'])) {
	$start_interest_date = $payment['interest_end'];
}



$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_payment", "Payment");
$modal->initiForm("form_payment");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.trust_receipt.tr.payment()")
));
$modal->SetVariable(array(
	array("transfer_id", $transfer['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "custom",
			"html" => $html
		)

	),
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
			"readonly" => true,
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
