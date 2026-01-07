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
    }else if($_POST['time_start']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input Start End!'
		));
    }else{
        $data = array(
            '#id' => "DEFAULT",
            '#created' => 'NOW()',
			'#updated' => 'NOW()',
            'round' => $_POST['round'],
            'furnace' => $_POST['furnace'],
            'crucible' => $_POST['crucible'],
            'amount' => $_POST['amount'],
            'date' => $_POST['date'],
            'time_start' => $_POST['time_start'],
            'time_end' => $_POST['time_end'],
            'remark' =>($_POST['remark']!="")?$_POST['remark']:"NULL",
            'user' => $_POST['user'],
            '#status' => 0
        );

        $data2 = array(
            '#id' => "DEFAULT",
            'crucible_id' => $_POST['crucible'],
            'round' => $_POST['round'],
            '#created' => 'NOW()',
			'#updated' => 'NOW()',
            'date' => $_POST['date']
        );

        if($dbc->Insert("bs_productions_furnace",$data)){
			$furnace_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $furnace_id
		));
        $dbc->Insert("bs_crucible_items",$data2);

        $furnace= $dbc->GetRecord("bs_productions_furnace","*","id=".$furnace_id);
        $os->save_log(0,$_SESSION['auth']['user_id'],"bs_productions_furnace-add",$furnace_id,array("bs_productions_furnace" => $furnace));
        }else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
    }
?>