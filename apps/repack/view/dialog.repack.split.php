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
		global $os;
		$dbc = $this->dbc;

		$pack = $dbc->GetRecord("bs_packing_items", "*", "id=" . $this->param['id']);
		echo '<form name="form_splitrepack" class="form-horizontal" role="form" onsubmit=";return false;">';
		echo '<input type="hidden" name="id" value="' . $pack['id'] . '">';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">จำนวนเดิม</label>';
		echo '<div class="col-sm-10">';
		echo '<input type="" class="form-control" name="split" value="' . $pack['weight_actual'] . '" readonly>';
		echo '</div>';
		echo '</div>';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">จำนวนใหม่</label>';
		echo '<div class="col-sm-10">';
		echo '<input type="" class="form-control" name="split_new" placeholder="จำนวนที่ต้องการแบ่งใหม่" readonly>';
		echo '</div>';
		echo '</div>';



		echo '<div class="m-2 row">';


		$aPacking = json_decode($os->load_variable("aPacking", "json"), true);

		echo '<div class="col-sm-8">';
		$select_packtype = '<select name="pack_name" class="form-control mr-2">';
		foreach ($aPacking as $pack) {
			$readonly = isset($pack['readonly']) ? $pack['readonly'] : true;
			$select_packtype .=  '<option data-value="' . $pack['value'] . '" data-readonly="' . ($readonly ? "true" : "false") . '">' . $pack['name'] . '</option>';
		}
		$select_packtype .= '</select>';
		echo $select_packtype;
		echo '</div>';
		echo '<div class="col-sm-2"><button class="btn btn-primary" onclick="fn.app.repack.repack.append()">Append</button></div>';
		echo '</div>';
		echo '<table id="tblSpliter" class="table table-brodered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">หมายเลขถุง</th>';
		echo '<th class="text-center">ประเภทถุง</th>';
		echo '<th class="text-center">ชนิดถุง</th>';
		echo '<th class="text-center">น้ำหนักถุง</th>';
		echo '<th class="text-center">น้ำหนักจริง</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody></tbody>';
		echo '</table>';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setExtraClass("modal-lg");
$modal->setModel("dialog_split_repack", "Split Repack");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Split", "fn.app.repack.repack.split()")
));
$modal->EchoInterface();

$dbc->Close();
