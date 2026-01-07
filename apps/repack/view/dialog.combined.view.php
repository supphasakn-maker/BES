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

		//$os->create_table_from_record($packing,"table table-sm table-bordered");

		echo '<table class="table table-striped table-bordered table-hover table-middle" width="100%">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center hidden-xs">';
		echo '</th>';
		echo '<th class="text-center">รอบการผลิต</th>';
		echo '<th class="text-center">หมายเลขถุง</th>';
		echo '<th class="text-center">ขนาด</th>';
		echo '<th class="text-center">ประเภท</th>';
		echo '<th class="text-center">นำหนัก</th>';
		echo '<th class="text-center">นำหนักจริง</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		$sql = "SELECT * FROM bs_packing_items WHERE parent = " . $this->param['id'];
		$rst = $dbc->Query($sql);
		while ($item = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td>' . $item['id'] . '</td>';
			echo '<td>' . $item['production_id'] . '</td>';
			echo '<td>' . $item['code'] . '</td>';
			echo '<td>' . $item['pack_name'] . '</td>';
			echo '<td>' . $item['pack_type'] . '</td>';
			echo '<td>' . $item['weight_expected'] . '</td>';
			echo '<td>' . $item['weight_actual'] . '</td>';
			echo '</tr>';
		}


		echo '</tbody>';
		echo '</table>';



		//$sql = "SELECT * FROM bs_mapping_silver_orders WHERE order_id = order.id";

	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_packing_combine", "Packing Combine");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
