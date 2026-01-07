<?php
	session_start();
	include_once "../config/define.php";
	include_once "../include/db.php";
	include_once "../include/oceanos.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	$st = $os->auth['setting'];
?>
	$.extend( $.fn.dataTable.defaults, {
		"pageLength": <?php echo isset($st['config']['datatable']['row'])?$st['config']['datatable']['row']:"10";?>
	} );
	
<?php
	$dbc->Close();
?>

