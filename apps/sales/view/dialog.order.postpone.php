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
	public $is_super_user = false;

	function body()
	{
		$dbc   = $this->dbc;
		$order = $dbc->GetRecord("bs_orders", "*", "id=" . intval($this->param['id']));
		$iform = new iform($this->dbc, $this->auth);

		$user_id = intval($_SESSION['auth']['user_id']);
		$user_data = $dbc->GetRecord("os_users", "*", "id=" . $user_id);

		if ($user_data && isset($user_data['gid'])) {
			$user_gid = intval($user_data['gid']);

			if (in_array($user_gid, array(1))) {
				$this->is_super_user = true;
			}
		}

		$today         = date("Y-m-d");
		$current_time  = date("H:i");

		$delivery_date_raw = isset($order['delivery_date']) ? trim($order['delivery_date']) : '';
		$delivery_date     = $delivery_date_raw ? date("Y-m-d", strtotime($delivery_date_raw)) : null;

		if (!$this->is_super_user && $delivery_date) {
			if ($delivery_date < $today) {
				echo '<div class="alert alert-danger mb-3">
						ไม่สามารถเลื่อนวันได้ เนื่องจากวันที่ส่งเดิม (' . date("d/m/Y", strtotime($delivery_date)) . ') ผ่านมาแล้ว
					  </div>';
				$this->disable_postpone_button = true;
			} elseif ($delivery_date === $today && $current_time >= "18:30") {
				echo '<div class="alert alert-danger mb-3">
						ไม่สามารถเลื่อนวันได้ เนื่องจากเลยเวลา 18:30 ของวันที่ส่งเดิมแล้ว
					  </div>';
				$this->disable_postpone_button = true;
			}
		}

		if ($this->is_super_user && $delivery_date) {
			$show_warning = false;
			$warning_msg = '';

			if ($delivery_date < $today) {
				$show_warning = true;
				$warning_msg = 'วันที่ส่งเดิม (' . date("d/m/Y", strtotime($delivery_date)) . ') ผ่านมาแล้ว';
			} elseif ($delivery_date === $today && $current_time >= "18:30") {
				$show_warning = true;
				$warning_msg = 'เลยเวลา 18:30 ของวันที่ส่งเดิมแล้ว';
			}

			if ($show_warning) {
				echo '<div class="alert alert-warning mb-3">
						<i class="fas fa-exclamation-triangle"></i> 
						<strong>คำเตือน:</strong> ' . $warning_msg . ' 
						<br><small>คุณมีสิทธิ์พิเศษในการเลื่อนวันได้</small>
					  </div>';
			}
		}

		$minDate = $today;

		$newDateDefault = ($delivery_date && !$this->disable_postpone_button) ? $delivery_date : $today;
?>
		<form name="form_postponeorder">
			<table class="table table-bordered table-form">
				<input type="hidden" name="id" value="<?php echo intval($order['id']); ?>">
				<tbody>
					<tr>
						<td><label>หมายเลข Order</label></td>
						<td class="p-2"><?php echo htmlspecialchars($order['code']); ?></td>
					</tr>
					<tr>
						<td><label>วันที่ส่งเดิม</label></td>
						<td class="p-2">
							<?php
							echo $delivery_date
								? date("d/m/Y", strtotime($delivery_date))
								: '<span class="text-muted">-</span>';
							?>
						</td>
					</tr>
					<tr>
						<td><label>วันที่ส่งใหม่</label></td>
						<td>
							<input
								name="delivery_date"
								type="date"
								class="form-control text-right"
								value="<?php echo htmlspecialchars($newDateDefault); ?>"
								min="<?php echo htmlspecialchars($minDate); ?>"
								<?php echo $this->disable_postpone_button ? 'disabled' : ''; ?>>
						</td>
					</tr>
					<tr>
						<td><label>เหตุผลของลูกค้า</label></td>
						<td>
							<?php
							$iform->EchoItem(array(
								"type"   => "comboboxdatabank",
								"source" => "db_postpone_reason_customer",
								"name"   => "reason_customer",
								"attr"   => $this->disable_postpone_button ? array("disabled" => "disabled") : array()
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>เหตุผลของบริษัท</label></td>
						<td>
							<?php
							$iform->EchoItem(array(
								"type"   => "comboboxdatabank",
								"source" => "db_postpone_reason_company",
								"name"   => "reason_company",
								"attr"   => $this->disable_postpone_button ? array("disabled" => "disabled") : array()
							));
							?>
						</td>
					</tr>
				</tbody>
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

$buttons = array(
	array("close", "btn-secondary", "ปิด")
);

if (!$modal->disable_postpone_button) {
	$buttons[] = array("action", "btn-outline-dark", "ย้ายวัน", "fn.app.sales.order.postpone()");
}

$modal->setModel("dialog_postpone_order", "เลื่อนการส่ง");
$modal->setButton($buttons);

$modal->EchoInterface(function () use ($body_content) {
	echo $body_content;
});

$dbc->Close();
?>