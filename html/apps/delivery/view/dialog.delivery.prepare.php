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
		$iform = new iform($dbc, $this->auth);
		$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $this->param['id']);
		$order = $dbc->GetRecord("bs_orders", "*", "delivery_id=" . $this->param['id']);
		echo '<form name="form_addpacking" data-iterator="1">';
		echo '<table class="table table-sm">';
		echo '<tr>';
		echo '<th class="text-center">ใบส่งของ</th>';
		echo '<th class="text-center">หมายเลขบิล</th>';
		echo '<th class="text-center">ชื่อลูกค้า</th>';
		echo '<th class="text-center">จำนวน</th>';
		echo '<th class="text-center">วันที่ส่ง</th>';
		echo '</tr>';
		echo '<tr>';
		echo '<td class="text-center">';
		echo $delivery['code'];
		echo '</td>';
		echo '<td class="text-center">';
		echo $delivery['code'];
		echo '</td>';
		echo '<td class="text-center">';
		echo $order['customer_name'];
		echo '</td>';
		echo '<td class="text-center">';
		echo $delivery['amount'];
		echo '</td>';
		echo '<td class="text-center">';
		echo $delivery['delivery_date'];
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '<hr>';
		echo '<table id="tblDriver" class="table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">ผู้จัดส่ง 1</th>';
		echo '<td class="text-center"><a onclick="fn.app.delivery.delivery.prepare_append_driver()" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i></a></td>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		echo '<td class="form-inline">';
		$iform->EchoItem(array(
			"name" => "driver[]",
			"type" => "comboboxdb",
			"source" => array(
				"name" => "fullname",
				"value" => "id",
				"table" => "bs_employees",
				"where" => "department = 2"
			)
		));
		echo '<a class="btn btn-danger"><i class="fa fa-trash"></i></a>';
		echo '</td>';
		echo '</tr>';
		echo '</tbody>';
		echo '</table>';

		echo '<hr>';
		echo '<div class="container">';
		echo '<table id="tblPackitem" class="table table-form table-sm table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">รหัสถุง</th>';
		echo '<th class="text-center">น้ำหนักแพ็คเสร็จ</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		$sql = "SELECT * FROM bs_packing_items WHERE status=1";
		$rst = $dbc->Query($sql);
		while ($item = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-center">' . $item['code'] . '</td>';
			echo '<td><input class="form-control form-control-xs"  type="text" name="items[]"></td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';

		echo '';
		echo '';
		echo '';
		echo '';
		echo '';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setExtraClass("modal-lg");
$modal->setModel("dialog_prepare_delivery", "Prepare Delivery");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Prepare", "fn.app.delivery.delivery.prepare()")
));
$modal->EchoInterface();

$dbc->Close();
