<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	include "../include/const.php";

	$title = $aReportType[$_POST['type']];
	$subtitle = "ประจำดือน ".date("F Y",strtotime($_POST['date']));
	/*
	$period = $_POST['period'];
	switch($period){
		case "daily":
			$subtitle = "ประจำวันที่ ".date("d/m/Y",strtotime($_POST['date']));
			break;
		case "monthly":
			$subtitle = "ประดือน ".date("F Y",strtotime($_POST['month']));
			break;
		case "yearly":
			$subtitle = "ประจำปี ".$_POST['year'];
			break;
		case "custom":	
			$subtitle = "ตั้งแต่วันที่ ".date("d/m/Y",strtotime($_POST['date_from']))." ถึงวันที่ ".date("d/m/Y",strtotime($_POST['date_to']));
	}
	*/

?>
<section class="text-center">
	<h1><?php echo $title;?></h1>
	<p><?php echo $subtitle;?> </p>
</section>
<div>
<?php
	include "report.".$_POST['type'].".php";
?>
</div>
<?php
	$dbc->Close();
?>












