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

    if($_POST['time_start']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input Start Time!'
		));
    }else{
        $data = array(
            '#id' => "DEFAULT",
            '#created' => 'NOW()',
			'#updated' => 'NOW()',
            'round' => $_POST['round'],
            'oven' => $_POST['oven'],
            'date' => $_POST['date'],
            'time_start' => $_POST['time_start'],
            'time_end' => $_POST['time_end'],
            'temp' => ($_POST['temp']!="")?$_POST['temp']:"NULL",
            'remark' =>($_POST['remark']!="")?$_POST['remark']:"NULL",
            'user' => $_POST['user'],
            '#status' => 0
        );
        if($dbc->Insert("bs_productions_oven",$data)){
			$oven_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $oven_id
			));
        $oven= $dbc->GetRecord("bs_productions_oven","*","id=".$oven_id);
        $os->save_log(0,$_SESSION['auth']['user_id'],"bs_productions_oven-add",$oven_id,array("bs_productions_oven" => $oven));
        }else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
    }
?>