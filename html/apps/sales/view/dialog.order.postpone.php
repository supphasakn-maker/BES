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
	public $disable_postpone_button = false;
	public $order;

	function body()
	{
		$dbc = $this->dbc;
		$order = $dbc->GetRecord("bs_orders", "*", "id=" . $this->param['id']);
		$iform = new iform($this->dbc, $this->auth);

		$today = date("Y-m-d");
		$current_time = date("H:i");

		if ($order['delivery_date'] == $today && $current_time >= "17:30") {
			echo '<div class="alert alert-danger">ไม่สามารถเลื่อนวันได้ เนื่องจากเลยเวลา 17:30 ของวันที่ส่งแล้ว</div>';
			$this->disable_postpone_button = true;
		}
?>
		<form name="form_postponeorder">
			<table class="table table-bordered table-form">
				<input type="hidden" name="id" value="<?php echo $order['id']; ?>">
				<tbody>
					<tr>
						<td><label>หมายเลข Order</label></td>
						<td class="p-2"><?php echo $order['code']; ?></td>

					</tr>
					<tr>
						<td><label>วันที่ส่งเดืม</label></td>
						<td class="p-2"><?php echo date("d/m/Y", strtotime($order['delivery_date'])); ?></td>
					</tr>
					<tr>
						<td><label>วันที่ส่งใหม่</label></td>
						<td><input name="delivery_date" type="date" class="form-control text-right" value="<?php echo $order['delivery_date']; ?>" min="<?php echo date('Y-m-d'); ?>"></td>

					</tr>
					<tr>
						<td><label>เหตุผลของลูกค้า</label></td>
						<td>
							<?php
							$iform->EchoItem(array(
								"type" => "comboboxdatabank",
								"source" => "db_postpone_reason_customer",
								"name" => "reason_customer",
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>เหตุผลของบริษัท</label></td>
						<td>
							<?php
							$iform->EchoItem(array(
								"type" => "comboboxdatabank",
								"source" => "db_postpone_reason_company",
								"name" => "reason_company",
							));
							?>
						</td>
					</tr>

			</table>
		</form>

<?php
	}
}

$modal = new myModel($dbc, $auth);
$modal->setParam($_POST);
ob_start();
$modal->body();
$body_content = ob_get_clean();

// ✅ แล้วค่อยกำหนดปุ่ม
$buttons = array(
	array("close", "btn-secondary", "Dismiss")
);

if (!$modal->disable_postpone_button) {
	$buttons[] = array("action", "btn-outline-dark", "ย้ายวัน", "fn.app.sales.order.postpone()");
}

$modal->setModel("dialog_postpone_order", "เลื่อนการส่ง");
$modal->setButton($buttons);

// ✅ ส่ง body ที่เราบันทึกไว้เมื่อกี้เข้า echo ด้วย
$modal->EchoInterface(function () use ($body_content) {
	echo $body_content;
});

$dbc->Close();
?>