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

	foreach($_POST['items'] as $item){
		$produce = $dbc->GetRecord("bs_productions","*","id=".$item);
		$dbc->Delete("bs_productions","id=".$item);
		$dbc->Delete("bs_packing_items","production_id=".$item);
		
		$dbc->Update("bs_imports",array(
			"#production_id" => "NULL",
			"#delivery_time" => "NULL",
			"#delivery_note" => "NULL",
			"#weight_in" => "NULL",
			"#weight_actual" => "NULL",
			"#weight_margin" => "NULL",
			"#bar" => "NULL",
			"#weight_bar" => "NULL",
			"#type" => 0
		),"production_id=".$item);
		
		$os->save_log(0,$_SESSION['auth']['user_id'],"produce-delete",$id,array("produces" => $produce));
	}

	$dbc->Close();
?>
