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
	
	$panel->setApp("project","Project");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'core');
	//$panel->setSection(isset($_GET['section'])?$_GET['section']:'overview');
	
	
	switch($panel->getView()){
		default:
			include "view/page.main.php";
			break;
	}
	
?>
  
      <script>
        var plugins = [
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/select2/css/select2.min.css',
			'plugins/select2/js/select2.min.js',
			'plugins/moment/moment.min.js'
        ]
        App.loadPlugins(plugins, null).then(() => {
	
        }).then(() => App.stopLoading())
      </script>
	  <style>
        .main-body {
          padding: 0;
        }
      </style>
<?php
	$dbc->Close();
?>