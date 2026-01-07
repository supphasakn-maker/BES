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

$order_id = intval($_POST['id'] ?? 0);
if ($order_id <= 0) {
	echo '<div class="alert alert-danger">Invalid Order ID</div>';
	$dbc->Close();
	exit;
}

$order = $dbc->GetRecord("bs_orders", "*", "id=" . $order_id);

if (!$order) {
	echo '<div class="alert alert-danger">ไม่พบข้อมูล Order</div>';
	$dbc->Close();
	exit;
}

class myModel extends imodal
{
	function body()
	{
		global $os;
		$dbc = $this->dbc;
		$order_id = intval($this->param['id'] ?? 0);
		$order = $dbc->GetRecord("bs_orders", "*", "id=" . $order_id);

		if (!$order) {
			echo '<div class="alert alert-danger">ไม่พบข้อมูล Order</div>';
			return;
		}

?>
		<table class="table table-bordered mb-0">
			<tbody>
				<tr>
					<th class="text-right" width="30%">หมายเลขจัดส่ง</th>
					<td><?php echo htmlspecialchars($order['code'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<th class="text-right">ชื่อลูกค้า</th>
					<td><?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<th class="text-right">วันที่สั่งซื้อ</th>
					<td><?php echo htmlspecialchars($order['created'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<th class="text-right">จำนวนที่จัดก่อนแบ่ง</th>
					<td><strong><?php echo number_format($order['amount'], 2); ?></strong></td>
				</tr>
			</tbody>
		</table>

		<div class="m-2">
			<div class="alert alert-info" id="split_total_display">
				ผลรวม: 0.00 / <?php echo number_format($order['amount'], 2); ?>
			</div>
		</div>

		<div class="m-2">
			<a href="javascript:void(0);" class="btn btn-primary" onclick="fn.app.sales.order.append_split()">
				<i class="fa fa-plus"></i> แยกบิล
			</a>
		</div>

		<form name="form_splitorder">
			<?php
			$delivery_count = 0;
			if (!empty($order['delivery_id']) && $order['delivery_id'] > 0) {
				$getdelivery = $dbc->GetRecord(
					"bs_delivery_pack_items",
					"COUNT(delivery_id) as bb",
					"delivery_id=" . intval($order['delivery_id'])
				);
				$delivery_count = intval($getdelivery['bb'] ?? 0);
			}
			?>
			<input type="hidden" name="delivery_id" value="<?php echo $delivery_count; ?>">
			<input type="hidden" name="id" value="<?php echo $order_id; ?>">
			<input type="hidden" name="total" value="<?php echo $order['amount']; ?>">

			<div class="table-responsive">
				<table class="table table-bordered table-form">
					<thead class="thead-light">
						<tr>
							<th width="20%">รายการ</th>
							<th width="25%">จำนวน</th>
							<th width="20%">วันส่ง</th>
							<th width="30%">หมายเหตุ</th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><label class="mb-0">ใบที่หนึ่ง</label></td>
							<td>
								<input
									data-name="amount"
									name="amount[]"
									type="number"
									step="0.01"
									min="0.01"
									class="form-control text-right"
									value="<?php echo $order['amount']; ?>"
									required
									placeholder="0.00">
							</td>
							<td>
								<input
									data-name="date"
									name="date[]"
									type="date"
									class="form-control"
									value="<?php echo htmlspecialchars($order['delivery_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
							</td>
							<td>
								<input
									name="remark[]"
									type="text"
									class="form-control"
									placeholder="หมายเหตุ (ถ้าม)"
									value="">
							</td>
							<td class="text-center">
								<span class="badge badge-secondary">หลัก</span>
							</td>
						</tr>
						<tr>
							<td><label class="mb-0">ใบแยก</label></td>
							<td>
								<input
									data-name="amount"
									name="amount[]"
									type="number"
									step="0.01"
									min="0.01"
									class="form-control text-right"
									value=""
									required
									placeholder="0.00">
							</td>
							<td>
								<input
									data-name="date"
									name="date[]"
									type="date"
									class="form-control"
									value="<?php echo htmlspecialchars($order['delivery_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
							</td>
							<td>
								<input
									name="remark[]"
									type="text"
									class="form-control"
									placeholder="หมายเหตุ (ถ้าม)"
									value="">
							</td>
							<td class="text-center p-1">
								<a
									href="javascript:void(0);"
									onclick="fn.app.sales.order.remove_split(this)"
									class="btn btn-sm btn-danger"
									title="ลบ">
									<i class="fa fa-times"></i>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="alert alert-warning">
				<strong>หมายเหตุ:</strong>
				<ul class="mb-0">
					<li>ผลรวมของจำนวนทั้งหมดต้องเท่ากับ <strong><?php echo number_format($order['amount'], 2); ?></strong></li>
					<li>ต้องมีอย่างน้อย 2 รายการ</li>
					<li>จำนวนแต่ละรายการต้องมากกว่า 0</li>
				</ul>
			</div>
		</form>
<?php
	}
}

$modal = new myModel($dbc, $os->auth);

$modal->setModel("dialog_split_order", "Split Order - แยกบิล");
$modal->initiForm("form_splitorder");
$modal->setExtraClass("modal-lg");
$modal->setParam($_POST);
$modal->setButton(array(
	array("close", "btn-secondary", "ยกเลิก"),
	array("action", "btn-primary", "บันทึกการแยก", "fn.app.sales.order.split()")
));

$modal->EchoInterface();
$dbc->Close();
?>