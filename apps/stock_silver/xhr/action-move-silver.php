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

	$dd = date("Y-m-d");

    $data = array(
        "stock" => 'BWF',
		"swapdate" => $dd

    );
	foreach($_POST['items'] as $item){

		$plan = $dbc->GetRecord("bs_stock_silver","*","id=".$item);
		$dbc->Update("bs_stock_silver",$data,"id=".$item);
        echo json_encode(array(
            'success'=>true
        ));
		$os->save_log(0,$_SESSION['auth']['user_id'],"bs_stock_silver-move",$id,array("silver_move" => $plan));
	}

	$dbc->Close();
?>
