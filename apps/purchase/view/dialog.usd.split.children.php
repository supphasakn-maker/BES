<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
		$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $this->param['id']);


		echo '<h3>Splited From</h3>';
		echo '<table class="table table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">ID</th>';
		echo '<th class="text-center">Date</th>';
		echo '<th class="text-center">Bank</th>';
		echo '<th class="text-center">Type</th>';
		echo '<th class="text-center">amount</th>';
		echo '<th class="text-center">rate_exchange</th>';
		echo '<th class="text-center">comment</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		$sql = "SELECT * FROM bs_purchase_usd WHERE parent = " . $usd['id'];
		$rst = $dbc->Query($sql);
		while ($children = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-center">' . $children['id'] . '</td>';
			echo '<td class="text-center">' . $children['confirm'] . '</td>';
			echo '<td class="text-center">' . $children['bank'] . '</td>';
			echo '<td class="text-center">' . $children['type'] . '</td>';
			echo '<td class="text-center">' . $children['amount'] . '</td>';
			echo '<td class="text-center">' . $children['rate_exchange'] . '</td>';
			echo '<td class="text-center">' . $children['comment'] . '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_split_usd_view", "Split View");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
