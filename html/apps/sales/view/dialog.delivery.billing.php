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
$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $_POST['id']);


class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
		$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $this->param['id']);

		echo '<form name="form_billingdelivery" class="form-horizontal" onsubmit=";return false;">';
		echo '<input type="hidden" name="id" value="' . $delivery['id'] . '">';

		if (is_null($delivery['billing_id'])) {
			echo '<div class="form-group row">';
			echo '<label class="col-sm-2 col-form-label text-right">Billing ID</label>';

			echo '<div class="col-sm-10">';
			echo '<input type="" class="form-control" name="billing_id[]" placeholder="หมายเลขบิล" value="">';
			echo '</div>';
			echo '</div>';
		} else {
			$billing = explode(",", $delivery['billing_id']);
			for ($i = 0; $i < count($billing); $i++) {
				echo '<div class="form-group row">';
				echo '<label class="col-sm-2 col-form-label text-right">Billing ID</label>';
				if ($i > 0) {
					echo '<div class="col-sm-8">';
					echo '<input type="" class="form-control" name="billing_id[]" placeholder="หมายเลขบิล" value="' . $billing[$i] . '">';
					echo '</div>';
					echo '<div class="col-sm-2"><button onclick="$(this).parent().parent().remove();" class="btn btn-danger">Remove</button></div>';
				} else {
					echo '<div class="col-sm-10">';
					echo '<input type="" class="form-control" name="billing_id[]" placeholder="หมายเลขบิล" value="' . $billing[$i] . '">';
					echo '</div>';
				}

				echo '</div>';
			}
		}
		echo '</form>';
	}
}


$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_billing_delivery", "Billing");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("action", "btn-primary", "Add Bill", "fn.app.sales.delivery.append_billing()"),
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sales.delivery.billing()")
));

$modal->EchoInterface();
$dbc->Close();
