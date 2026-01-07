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
		$packing = $dbc->GetRecord("bs_packing_items", "*", "id=" . $this->param['id']);

		// $os->create_table_from_record($packing,"table table-sm table-bordered");

		//$sql = "SELECT * FROM bs_mapping_silver_orders WHERE order_id = order.id";

		echo '<table class="table table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">หมายเลขถุง</th>';
		echo '<th class="text-center">ประเภทถุง</th>';
		echo '<th class="text-center">Type</th>';
		echo '<th class="text-center">น้ำหนักปัจจุบัน</th>';
		echo '<th class="text-center">น้ำหนักจริง</th>';
		echo '</tru>';
		echo '</thead>';
		echo '<tbody>';
		$total = 0;
		$sql = "SELECT * FROM bs_packing_items WHERE id = " . $packing['id'];

		$rst = $dbc->Query($sql);
		while ($line = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-left">' . $line['code'] . '</td>';
			echo '<td class="text-left">' . $line['pack_name'] . '</td>';
			echo '<td class="text-left">' . $line['pack_type'] . '</td>';
			echo '<td class="text-center">' . number_format($line['weight_actual'], 0) . '</td>';
			echo '<td class="text-center">' . number_format($line['weight_expected'], 0) . '</td>';
			// $total += $line['size']*$line['amount'];
			echo '</tr>';
		}
		echo '</tbody>';
		echo '<tfoot>';
		echo '<tr>';
		echo '<th colspan="3"></th>';
		echo '<th></th>';
		echo '<th></th>';
		echo '</tr>';
		echo '</tfoot>';
		echo '</table>';
	}
}
$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_packing_split", "Spltied Detail");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
