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

?>
		<div class="btn-area btn-group mb-2">
			<form name="combine_filer" class="form-inline" onsubmit='fn.app.sales.delivery.combine_reload();return false;'>
				<label class="mr-sm-2">Customer</label>
				<select class="form-control mr-sm-2" name="customer">
					<?php
					$sql = "SELECT * FROM bs_customers";
					$rst = $dbc->Query($sql);
					while ($customer = $dbc->Fetch($rst)) {
						echo '<option value="' . $customer['id'] . '">' . $customer['name'] . '</option>';
					}
					?>
				</select>

				<label class="mr-sm-2">Delivery Date</label>
				<input name="delivery_date" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d"); ?>">
				<button type="submit" class="btn btn-primary">Lookup</button>
			</form>
		</div>
		<table id="tblOrder" class="table table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_order" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Order</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Price</th>
					<th class="text-center">Vat</th>
					<th class="text-center">Total</th>
					<th class="text-center">Delivery</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
<?php
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_combine_delivery", "Combine Order");

$modal->setExtraClass("modal-full");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Combine", "fn.app.sales.delivery.combine()")
));
$modal->EchoInterface();

$dbc->Close();
?>