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
	$panel = new ipanel($dbc,$os->auth);
	$ui_form = new iform($dbc,$os->auth);

	$panel->setApp("report_match","Match");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'report_match');

?>
<div class="card">
	<div class="card-header border-bottom">
		<h5 class="card-title p-2"><i class="far fa-link mr-2" aria-hidden="true"></i>Match</h5>
	</div>
	<div class="card-body">
		<div class="btn-area btn-group mb-2">
            <input type="date" name="date" class="form-control" value="<?php echo date("Y-m-d")?>">
			<button class="btn btn-danger" onclick="$('input[name=date]').change()">Lookup</button>
        </div>
		<div id="display_area">
		</div>
	</div>
</div>
<script>
	var plugins = [
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/select2/js/select2.min.js',
		'plugins/moment/moment.min.js'
	];
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
        <?php
		include "control/controller.view.js";
        ?>
	
	}).then(() => App.stopLoading())
</script>
