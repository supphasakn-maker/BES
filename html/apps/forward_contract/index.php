<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/datastore.php";



$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$panel = new ipanel($dbc, $os->auth);
$ui_form = new iform($dbc, $os->auth);

include_once "../../include/alert-buysell.php";

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

?>
<div class="row gutters-sm">
	<div class="col-xl-12 mb-3">
		<div class="row gutters-sm">
			<div class="col-xl-6 mb-3">
				<?php include "view/card.contract.add.php"; ?>
			</div>
			<div class="col-xl-6 mb-3">
				<?php include "view/card.transfer.php"; ?>
			</div>
		</div>
	</div>

</div>
<script>
	var plugins = [
		'apps/import/include/interface.js',
		'apps/customer/include/interface.js',
		'apps/purchase/include/interface.js',
		'apps/sales_screen/include/interface.js',
		'apps/forward_contract/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/chart.js/Chart.min.js',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
		'plugins/select2/js/select2.full.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/sweetalert/sweetalert-dev.js',
		'plugins/sweetalert/sweetalert.css',
		'plugins/moment/moment.min.js'
	]


	App.loadPlugins(plugins, null).then(() => {

		<?php
		//include "control/controller.import.view.js";
		include "control/controller.contract.view.js";

		if ($os->allow("forward_contract", "add")) include "control/controller.contract.add.js";

		if ($os->allow("forward_contract", "edit")) include "control/controller.contract.edit.js";
		if ($os->allow("forward_contract", "edit")) include "control/controller.contract.edit_amount.js";
		if ($os->allow("forward_contract", "edit")) include "control/controller.contract.edit_adjust.js";
		if ($os->allow("forward_contract", "edit")) include "control/controller.contract.edit_product.js";
		if ($os->allow("forward_contract", "edit")) include "control/controller.contract.edit_trade.js";
		if ($os->allow("forward_contract", "edit")) include "control/controller.contract.edit_interest.js";
		if ($os->allow("forward_contract", "edit")) include "control/controller.import.lookup.js";
		if ($os->allow("forward_contract", "remove")) include "control/controller.contract.remove.js";


		?>
	}).then(() => App.stopLoading())
</script>