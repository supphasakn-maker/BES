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

		$removable = true;
		$packing = $dbc->GetRecord("bs_packing_items", "*", "id=" . $this->param['id']);
		$line = $dbc->GetRecord("bs_packing_items", "COUNT(id)", "parent = " . $packing['id']);
		echo '<form name="form_restorerepack" onsubmit="return 0;">';


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
		echo '<th class="text-center">Action</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		if ($line[0] > 0) {
			$sql = "SELECT * FROM bs_packing_items WHERE parent = " . $packing['id'];
			$rst = $dbc->Query($sql);
			while ($item = $dbc->Fetch($rst)) {
				echo '<input type="hidden" name="restore[]" value="' . $item['id'] . '">';
				echo '<tr>';
				echo '<td>' . $item['id'] . '</td>';
				echo '<td>' . $item['production_id'] . '</td>';
				echo '<td>' . $item['code'] . '</td>';
				echo '<td>' . $item['pack_name'] . '</td>';
				echo '<td>' . $item['pack_type'] . '</td>';
				echo '<td>' . $item['weight_expected'] . '</td>';
				echo '<td>' . $item['weight_actual'] . '</td>';
				echo '<td class="text-center">';
				if ($dbc->HasRecord("bs_delivery_pack_items", "item_id=" . $item['id'])) {
					echo '<span class="badge badge-danger">ถูกใช้แล้ว</span>';
					$removable = false;
				} else {
					echo '<span class="badge badge-warning">กู้คืน</span>';
				}
				echo '</td>';
				echo '</tr>';
			}

			echo '<input type="hidden" name="remove[]" value="' . $packing['id'] . '">';
			echo '<tr class="bg-danger text-white">';
			echo '<td>' . $packing['id'] . '</td>';
			echo '<td>' . $packing['production_id'] . '</td>';
			echo '<td>' . $packing['code'] . '</td>';
			echo '<td>' . $packing['pack_name'] . '</td>';
			echo '<td>' . $packing['pack_type'] . '</td>';
			echo '<td>' . $packing['weight_expected'] . '</td>';
			echo '<td>' . $packing['weight_actual'] . '</td>';
			echo '<td class="text-center">';
			echo '<span class="badge badge-dark">ลบ</span>';
			echo '</td>';
			echo '</tr>';
		} else if ($packing['parent'] != null) {

			$sql = "SELECT * FROM bs_packing_items WHERE parent = " . $packing['parent'];
			$rst = $dbc->Query($sql);
			while ($item = $dbc->Fetch($rst)) {
				echo '<input type="hidden" name="remove[]" value="' . $item['id'] . '">';
				echo '<tr>';
				echo '<td>' . $item['id'] . '</td>';
				echo '<td>' . $item['production_id'] . '</td>';
				echo '<td>' . $item['code'] . '</td>';
				echo '<td>' . $item['pack_name'] . '</td>';
				echo '<td>' . $item['pack_type'] . '</td>';
				echo '<td>' . $item['weight_expected'] . '</td>';
				echo '<td>' . $item['weight_actual'] . '</td>';
				echo '<td class="text-center">';
				if ($dbc->HasRecord("bs_delivery_pack_items", "item_id=" . $item['id'])) {
					echo '<span class="badge badge-danger">ถูกใช้แล้ว</span>';
					$removable = false;
				} else {
					echo '<span class="badge badge-warning">ลบได้</span>';
				}
				echo '</td>';
				echo '</tr>';
			}

			$parent = $dbc->GetRecord("bs_packing_items", "*", "id=" . $packing['parent']);

			echo '<input type="hidden" name="restore[]" value="' . $parent['id'] . '">';
			echo '<tr class="bg-primary text-white">';
			echo '<td>' . $parent['id'] . '</td>';
			echo '<td>' . $parent['production_id'] . '</td>';
			echo '<td>' . $parent['code'] . '</td>';
			echo '<td>' . $parent['pack_name'] . '</td>';
			echo '<td>' . $parent['pack_type'] . '</td>';
			echo '<td>' . $parent['weight_expected'] . '</td>';
			echo '<td>' . $parent['weight_actual'] . '</td>';
			echo '<td class="text-center">';
			echo '<span class="badge badge-dark">กู้คืน</span>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '<input type="hidden" name="removable" value="' . ($removable ? "true" : "false") . '">';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_packing_restore", "Packing Restore");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-danger", "Restore", "fn.app.repack.repack.restore()")
));
$modal->EchoInterface();

$dbc->Close();
