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

	$sell = $_POST['sell'];
	$newsell = str_replace(",", "", $sell);

	$buy = $_POST['buy'];
	$newbuy = str_replace(",", "", $buy);

		$data = array(
			'no' => $_POST['no'],
            'date' => $_POST['date'],
			'rate_spot' => $_POST['rate_spot'],
            'rate_exchange' => $_POST['rate_exchange'],
			'rate_pmdc' => $_POST['rate_pmdc'],
            'sell' => $newsell,
            'buy' => $newbuy
		);

		if($dbc->Update("bs_announce_silver",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$tr = $dbc->GetRecord("bs_announce_silver","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"announce_silver-edit",$_POST['id'],array("trs" => $tr));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	

	$dbc->Close();
?>
