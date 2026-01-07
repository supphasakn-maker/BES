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
$order = $dbc->GetRecord("bs_quick_orders", "*", "id=" . $_POST['id']);

class myModel extends imodal
{
	private $os = null;

	function setOS($os)
	{
		$this->os = $os;
	}

	function body()
	{
		$dbc = $this->dbc;
		$this->initilForm("form_transformquick_order");
		$order = $dbc->GetRecord("bs_quick_orders", "*", "id=" . $this->param['id']);
		$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $order['customer_id']);
		$sel = function ($cur, $val) {
			return (string)$cur === (string)$val ? ' selected' : '';
		};


		echo '<ul class="list-group list-group-horizontal mb-3">';
		echo '<li class="list-group-item flex-fill text-center">';
		echo '<div class="text-secondary">ID</div><strong>' . $this->param['id'] . ' </strong>';
		echo '</li>';
		echo '<li class="list-group-item flex-fill text-center">';
		echo '<div class="text-secondary">Created</div><strong>' . $order['created'] . ' </strong>';
		echo '</li>';
		echo '<li class="list-group-item flex-fill text-center">';
		echo '<div class="text-secondary">Sales</div><strong>' . $this->os->auth['display'] . ' </strong>';
		echo '</li>';
		echo '</ul>';

		echo '<form name="form_transformquick_order">';
		echo '<input type="hidden" name="id" value="' . $order['id'] . '">';
		echo '<table class="table table-bordered table-form">';
		echo '<tbody>';
		echo '<tr>';
		echo '<td><label>ชื่อลูกค้า</label></td>';
		echo '<td><input class="form-control" readonly value="' . $customer['name'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>วันเวลาทำรายการ</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['created'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>จำนวน</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['amount'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>ราคา</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['price'] . '"></td>';
		echo '</tr>';
		echo '<td><label>ราคา USD</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['usd'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>ราคารวม ภาษีมูลค่าเพิ่ม 7%</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['total'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>spot</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['rate_spot'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>exchange</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['rate_exchange'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>remark</label></td>';
		echo '<td><input class="form-control" readonly value="' . $order['remark'] . '"></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><label>ลูกค้าต้องการฝากของ</label></td>';
		echo '<td>';
		echo '<input type="hidden" name="keepsilver" value="0">';
		echo '<input class="form-control keep" value="1" name="keepsilver" type="checkbox" id="keepsilver">';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><label>วันที่ส่งของ</label></td>';
		echo '<td><input name="delivery_date" value="" type="date" class="form-control delivery" style="width: 175px;" id="delivery_date"></td>';

		echo '</tr>';
		// เลือกรูปแบบการจัดส่ง (orderable_type)  // << add orderable_type
		$currentType = isset($order['orderable_type']) ? $order['orderable_type'] : '';
		echo '<tr>';
		echo '<td><label class="form-label">รูปแบบการจัดส่ง</label></td>';
		echo '<td>';
		echo '<select name="orderable_type" class="form-control orderable_type" readonly>';
		echo '<option value="">' . 'Select Delivery' . '</option>';
		echo '<option value="delivered_by_company"' . $sel($currentType, 'delivered_by_company') . '>จัดส่งโดยรถบริษัท</option>';
		echo '<option value="post_office"' . $sel($currentType, 'post_office') . '>จัดส่งโดยไปรษณีย์ไทย</option>';
		echo '<option value="receive_at_company"' . $sel($currentType, 'receive_at_company') . '>รับสินค้าที่บริษัท</option>';
		echo '<option value="receive_at_luckgems"' . $sel($currentType, 'receive_at_luckgems') . '>รับสินค้าที่ Luck Gems</option>';
		echo '</select>';
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>เวลาส่งของ</label></td>';
		echo '<td>';
		$this->iform->EchoItem(array(
			"type" => "comboboxdatabank",
			"source" => "db_time",
			"name" => "delivery_time",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			)
		));
		echo '</td>';
		echo '</tr>';



		echo '</tbody>';
		echo '</table>';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setOS($os);
$modal->setParam($_POST);
$modal->setModel("dialog_transform_quick_order", "Transform to Order");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Create Order", "fn.app.sales.quick_order.transform(" . $_POST['id'] . ")")
));



$modal->EchoInterface();
$dbc->Close();
?>
<script type="text/javascript">
	$(function() {
		$('.keep').on('change', function() {
			var checked = $(this).is(':checked');
			// console.log('Checkbox changed, checked:', checked);

			if (checked) {
				// If keeping the item, disable delivery fields and clear values
				$('.delivery').prop('disabled', true).val('');
				$("[name='delivery_time']").prop('disabled', true).val('none');
			} else {
				// If not keeping, enable delivery fields
				$('.delivery').prop('disabled', false);
				$("[name='delivery_time']").prop('disabled', false);
			}
		});

		// Initialize the state on page load
		$('.keep').trigger('change');
	});
</script>