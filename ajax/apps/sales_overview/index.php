<?php
	session_start();
	@ini_set('display_errors',1);
	include "../../config/define.php";
	include "../../include/db.php";
	include "../../include/oceanos.php";
	include "../../include/iface.php";
	include "../../include/demo.php";
	
	$demo = new demo;
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	//$os->initial_lang("lang");
	$panel = new ipanel($dbc,$os->auth);
	$panel->setApp("sales_overview","Sales Overview");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'main');
	
	$ui_form = new iform($dbc,$os->auth);
	
	$date = isset($_GET['date'])?$_GET['date']:date("Y-m-d");
	$today = time();
	
	$order_count = $dbc->GetRecord("bs_orders","COUNT(id)","date LIKE '".$date."'");
	$order_total = $dbc->GetRecord("bs_orders","SUM(amount)","date LIKE '".$date."'");
	
	switch($panel->getView()){
		case "delivery":include "view/page.delivery.php";break;
		default:include "view/page.main.php";break;
		
	}
	
?>

<script>
	var plugins = [
		'apps/sales/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/chart.js/Chart.min.js',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
	];


	App.loadPlugins(plugins, null).then(() => {
		App.checkAll();
		<?php
			include "../sales/control/controller.order.remove_each.js";
		?>
		
		/*
		$('.datatable').DataTable({
			"dom": '<"toolbar">rtp',
			//"pageLength": 5
		});
		*/
		
		
	}).then(() => App.stopLoading())
</script>