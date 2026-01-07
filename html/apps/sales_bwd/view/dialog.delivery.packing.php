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
		global $aPacking;
		$dbc = $this->dbc;
		$delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $this->param['id']);
		$order = $dbc->GetRecord("bs_orders_bwd", "*", "delivery_id=" . $delivery['id']);
		$items = array();


		if ($delivery['status'] == 1) {
			$edit_mode = true;
			$sql = "SELECT * FROM bs_bwd_pack_items WHERE delivery_id=" . $delivery['id'];
			$rst = $dbc->Query($sql);
			while ($item = $dbc->Fetch($rst)) {
				array_push($items, $item);
			}
		} else {
			$edit_mode = false;
		}

		echo '<ul class="list-group list-group-horizontal mb-3">';
		echo '<li class="list-group-item flex-fill text-center">';
		echo '<div class="text-secondary">ประเภท</div><strong>';
		if ($delivery['type'] == 1) {
			echo 'แบบธรรมดา';
		} else {
			echo 'แบบรวม';
		}
		echo '</strong>';
		echo '</li>';
		echo '<li class="list-group-item flex-fill text-center">';
		echo '<div class="text-secondary">คำสั่งซื้อ</div><strong>';
		if ($delivery['type'] == 2) {
			$sql = "SELECT * FROM bs_orders_bwd WHERE delivery_id=" . $order['delivery_id'];
			$rst = $dbc->Query($sql);
			while ($item = $dbc->Fetch($rst)) {
				echo '<div>' . $item['code'] . '</div>';
			}
		} else {
			echo $order['code'];
		}
		echo '</strong>';
		echo '</li>';
		echo '<li class="list-group-item flex-fill text-center">';
		echo '<div class="text-secondary">วันที่สั่งซื้อ</div><strong>' . $order['created'] . ' </strong>';
		echo '</li>';
		echo '<li class="list-group-item flex-fill text-center">';
		echo '<div class="text-secondary">วันที่ส่ง</div><strong>' . $delivery['delivery_date'] . ' </strong>';
		echo '</li>';
		echo '</ul>';

?>
		<form name="form_packing" data-items='<?php echo $edit_mode ? json_encode($items, JSON_UNESCAPED_UNICODE) : ""; ?>'>
			<div class="form-inline">
				<input type="hidden" name="id" value="<?php echo $delivery['id']; ?>">
				<input type="hidden" name="amount_limit" value="<?php echo $delivery['amount']; ?>">
				<input type="hidden" name="edit" value="<?php echo $edit_mode ? "true" : "false"; ?>">
			</div>
			<div class="row">
				<div class="col-md-5">
					<label>หมายเลขแท่ง</label>
					<select name="code_search" type="text" class="form-control" placeholder="Product Item"></select>
				</div>
				<div class="col-md-5 mt-4">
					<button onclick="fn.app.sales_bwd.delivery.mapping()" class="btn btn-primary mr-2">Append</button>
				</div>
			</div>
			<div class="row">
				<div class="col-8">
					<div>
						<table id="tblSilverDetail" data-id="<?php echo $delivery['id']; ?>" class="table table-form table-sm table-bordered">
							<thead>
								<tr>
									<th class="text-center">หมายเลขแท่ง</th>
									<th class="text-center">ประเภท</th>
									<th class="text-center">รายการ</th>
									<th class="text-center">นำหนัก</th>
									<th class="text-center">จำนวนแท่ง</th>
									<th class="text-center">ดำเนินการ</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>

		</form>

<?php
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_packing_delivery", "Stock Delivery");
$modal->setExtraClass("modal-full");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "SUBMIT", "fn.app.sales_bwd.delivery.packing()")
));
$modal->EchoInterface();

$dbc->Close();
?>
<script>
	$("[name=code_search]").select2({
		ajax: {
			url: 'apps/sales_bwd/xhr/action-load-item.php',
			dataType: 'json',
			data: function(d) {
				d.silver = $('select[name=round_filter]').val();
				return d;
			},
			processResults: function(data, params) {
				return {
					results: data.results
				};
			}
		}
	});
</script>
<script>
	fn.app.sales_bwd.delivery.mapping = function() {
		$.post("apps/sales_bwd/xhr/action-load-silver.php", {
			packing_id: $("[name=code_search]").val(),
			delivery_id: $("#tblSilverDetail").attr("data-id")

		}, function(response) {

			if (response.success) {
				$("#tblSilverDetail").DataTable().draw();
			} else {
				fn.notify.warnbox(response.msg, "Oops...");
			}
		}, "json");
		return false;
	};
</script>