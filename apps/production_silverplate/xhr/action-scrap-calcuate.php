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

        $line = $dbc->GetRecord("bs_scrap_items","SUM(weight_expected)","production_id = ".$_REQUEST['id']);
   
        $data = array(
            'weight_out_safe' => number_format($line[0],4),
			'weight_out_total' => number_format($line[0],4)
        );

		if($dbc->Update("bs_productions",$data,"id=".$_REQUEST['id'])){
			echo json_encode(array(
				'success'=>true
		));
        $oven= $dbc->GetRecord("bs_productions","*","id=".$oven_id);
        $os->save_log(0,$_SESSION['auth']['user_id'],"bs_productions-sumexport",$oven_id,array("bs_productions" => $oven));
        }else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
    
?>