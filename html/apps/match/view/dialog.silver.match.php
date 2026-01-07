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
		if (isset($this->param['sales']) && isset($this->param['purchase'])) {


			echo '<form name="form_matchsilver">';
			echo '<div class="form-group row">';
			echo '<label class="col-sm-2 col-form-label text-right">Mapping Date</label>';
			echo '<div class="col-sm-5">';
			echo '<input type="date" class="form-control" name="date" value="' . date("Y-m-d") . '">';
			echo '</div>';
			echo '</div>';
			echo '<div class="row">';
			echo '<div class="col-6">';
			echo '<table class="table table-sm table-bordered">';
			echo '<tfoot>';
			echo '<tr>';
			echo '<th colspan="3" class="text-right">';
			echo 'Total <span id="silver_total_match_order" class="badge">100</span>';

			echo '</th>';
			echo '</tr>';
			echo '</tfoot>';
			echo '<thead>';
			echo '<tr>';
			echo '<th class="text-center text-nowrap">Order ID</th>';
			echo '<th class="text-center text-nowrap">Order Amount</th>';
			echo '<th class="text-center text-nowrap">Match Amount</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach ($this->param['sales'] as $sales) {
				$order = $dbc->GetRecord("bs_orders", "*", "id=" . $sales);
				if ($dbc->HasRecord("bs_mapping_silver_orders", "order_id=" . $order['id'] . " AND mapping_id IS NULL")) {
					$mapping = $dbc->GetRecord("bs_mapping_silver_orders", "*", "order_id=" . $order['id'] . " AND mapping_id IS NULL");

					echo '<tr>';
					echo '<input type="hidden" name="order_mapping_id[]" value="' . $mapping['id'] . '">';
					echo '<input type="hidden" name="order_id[]" value="' . $order['id'] . '">';
					echo '<td class="text-center align-middle text-nowrap">' . $order['code'] . '</td>';
					echo '<td class="text-center align-middle">' . $mapping['amount'] . '/' . $order['amount'] . '</td>';
					echo '<td class="p-0">';
					$class = "form-control form-control-sm text-center rounded-0 border-dark";
					$onchange = "fn.app.match.silver.dialog_match_calculation()";
					echo '<input xname="order_amount" name="order_amount[]" class="' . $class . '" onchange="' . $onchange . '" value="' . $mapping['amount'] . '">';
					echo '</td>';
					echo '</tr>';
				} else {
					echo '<tr>';
					echo '<input type="hidden" name="order_mapping_id[]" value="">';
					echo '<input type="hidden" name="order_id[]" value="' . $order['id'] . '">';
					echo '<td class="text-center align-middle text-nowrap">' . $order['code'] . '</td>';
					echo '<td class="text-center align-middle">' . $order['amount'] . '</td>';
					echo '<td class="p-0">';
					$class = "form-control form-control-sm text-center rounded-0 border-dark";
					$onchange = "fn.app.match.silver.dialog_match_calculation()";
					echo '<input xname="order_amount" name="order_amount[]" class="' . $class . '" onchange="' . $onchange . '" value="' . $order['amount'] . '">';
					echo '</td>';
					echo '</tr>';
				}
			}
			echo '</tbody>';
			echo '</table>';
			echo '</div>';
			echo '<div class="col-6">';
			echo '<table class="table table-sm table-bordered">';
			echo '<tfoot>';
			echo '<tr>';
			echo '<th colspan="3" class="text-right">';
			echo 'Total <span id="silver_total_match_purchase" class="badge">100</span>';
			echo '</th>';
			echo '</tr>';
			echo '</tfoot>';
			echo '<thead>';
			echo '<tr>';
			echo '<th class="text-center">Purchase ID</th>';
			echo '<th class="text-center">Purchase Amount</th>';
			echo '<th class="text-center">Match Amount</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach ($this->param['purchase'] as $purchase) {
				$purchase = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $purchase);
				if ($dbc->HasRecord("bs_mapping_silver_purchases", "purchase_id=" . $purchase['id'] . " AND mapping_id IS NULL")) {
					$mapping = $dbc->GetRecord("bs_mapping_silver_purchases", "*", "purchase_id=" . $purchase['id'] . " AND mapping_id IS NULL");

					echo '<tr>';
					echo '<input type="hidden" name="purchase_mapping_id[]" value="' . $mapping['id'] . '">';
					echo '<input type="hidden" name="purchase_id[]" value="' . $purchase['id'] . '">';
					echo '<td class="text-center align-middle text-nowrap">' . $purchase['date'] . '</td>';
					echo '<td class="text-center align-middle">' . $mapping['amount'] . '/' . $purchase['amount'] . '</td>';
					echo '<td class="p-0">';
					$class = "form-control form-control-sm text-center rounded-0 border-dark";
					$onchange = "fn.app.match.silver.dialog_match_calculation()";
					echo '<input xname="purchase_amount" name="purchase_amount[]" class="' . $class . '" onchange="' . $onchange . '" value="' . $mapping['amount'] . '">';
					echo '</td>';
					echo '</tr>';
				} else {
					echo '<tr>';
					echo '<input type="hidden" name="purchase_mapping_id[]" value="">';
					echo '<input type="hidden" name="purchase_id[]" value="' . $purchase['id'] . '">';
					echo '<td class="text-center align-middle text-nowrap">' . $purchase['date'] . '</td>';
					echo '<td class="text-center align-middle">' . $purchase['amount'] . '</td>';
					echo '<td class="p-0">';
					$class = "form-control form-control-sm text-center rounded-0 border-dark";
					$onchange = "fn.app.match.silver.dialog_match_calculation()";
					echo '<input xname="purchase_amount" name="purchase_amount[]" class="' . $class . '" onchange="' . $onchange . '" value="' . $purchase['amount'] . '">';
					echo '</td>';
					echo '</tr>';
				}
			}
			echo '</tbody>';
			echo '</table>';
			echo '</div>';
			echo '<div class="col-12">';
			echo '<textarea name="remark" class="form-control" placeholder="Remark"></textarea>';
			echo '</div>';
			echo '</div>';
			echo '</form>';
		} else {
			echo '<div class="alert alert-danger">ข้อมูลไม่ครบ</div>';
		}
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_match_silver", "Match Silver");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Match", "fn.app.match.silver.match()")
));
$modal->EchoInterface();

$dbc->Close();
