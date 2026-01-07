<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/nebulaos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new nebulaos($dbc);

class myModel extends imodal
{
	function body()
	{
		global $os;
		$dbc = $this->dbc;
		$map_item = $dbc->GetRecord("bs_mapping_silvers", "*", "id=" . $this->param['id']);

		echo '<div class="row">';
		echo '<div class="col-sm-12">';
		$os->create_table_from_record($map_item, "table table-bordered table-striped", false);
		echo '</div>';
		echo '<div class="col-sm-6">';
		$sql = "SELECT * FROM bs_mapping_silver_orders WHERE mapping_id = " . $map_item['id'];
		$rst = $dbc->Query($sql);
		echo '<table class="table table-bordered table-striped">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">ID</th>';
		echo '<th class="text-center">Mapping ID</th>';
		echo '<th class="text-center">Order ID</th>';
		echo '<th class="text-center">Amount</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		while ($item = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-center">' . $item['id'] . '</td>';
			echo '<td class="text-center">' . $item['mapping_id'] . '</td>';
			echo '<td class="text-center">' . $item['order_id'] . '</td>';
			echo '<td class="text-center">' . $item['amount'] . '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';

		echo '<div class="col-sm-6">';

		$sql = "SELECT * FROM bs_mapping_silver_purchases WHERE mapping_id = " . $map_item['id'];
		$rst = $dbc->Query($sql);
		echo '<table class="table table-bordered table-striped">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">ID</th>';
		echo '<th class="text-center">Mapping ID</th>';
		echo '<th class="text-center">Purchase ID</th>';
		echo '<th class="text-center">Amount</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		while ($item = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-center">' . $item['id'] . '</td>';
			echo '<td class="text-center">' . $item['mapping_id'] . '</td>';
			echo '<td class="text-center">' . $item['purchase_id'] . '</td>';
			echo '<td class="text-center">' . $item['amount'] . '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';

		echo '</div>';


		echo '</div>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_lookup_silver", "Lookup Silver");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Lookup", "fn.app.match.silver.lookup()")
));
$modal->EchoInterface();

$dbc->Close();
