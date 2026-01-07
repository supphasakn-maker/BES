<?php
	session_start();
	@ini_set('display_errors',1);
	include "../../config/define.php";
	include "../../include/db.php";
	include "../../include/oceanos.php";
	include "../../include/iface.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	//$os->initial_lang("lang");
	$panel = new ipanel($dbc,$os->auth);
	$ui_form = new iform($dbc,$os->auth);
	
	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");
	
?>
<div class="row gutters-sm">
	
	<div class="col-xl-12">
		<?php include "view/card.tr.php";?>
	</div>
</div>
      <script>
        var plugins = [
			'apps/import/include/interface.js',
			'apps/customer/include/interface.js',
			'apps/purchase/include/interface.js',
			'apps/trust_receipt/include/interface.js',
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/chart.js/Chart.min.js',
			'plugins/jquery-sparkline/jquery.sparkline.min.js',
			'plugins/select2/js/select2.full.min.js',
			'plugins/select2/css/select2.min.css',
			'plugins/moment/moment.min.js'
        ]
		
	
        App.loadPlugins(plugins, null).then(() => {
			
			<?php
			include "control/controller.tr.view.js";
			//include "control/controller.spot.view.js";
			
			if($os->allow("trust_receipt","add"))include "control/controller.tr.add.js";
			if($os->allow("trust_receipt","edit"))include "control/controller.usd.lookup.js";
			if($os->allow("trust_receipt","edit"))include "control/controller.tr.edit.js";
			if($os->allow("trust_receipt","remove"))include "control/controller.tr.remove.js";
			
			?>
        }).then(() => App.stopLoading())
      </script>
