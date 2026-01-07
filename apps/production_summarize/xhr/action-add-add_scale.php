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

        $data = array(
            '#id' => "DEFAULT",
            '#created' => 'NOW()',
			'#updated' => 'NOW()',
            'round' => $_POST['round'],
            'scale' => $_POST['scale'],
            'date' => $_POST['date'],
            'approve_scale' => $_POST['approve_scale'],
            'approve_packing' => $_POST['approve_packing'],
            'approve_check' => $_POST['approve_check'],
            'remark' =>($_POST['remark']!="")?$_POST['remark']:"NULL",
            '#status' => 0
        );
        if($dbc->Insert("bs_productions_scale",$data)){
			$scale_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $scale_id
			));
        $scale= $dbc->GetRecord("bs_productions_scale","*","id=".$scale_id);
        $os->save_log(0,$_SESSION['auth']['user_id'],"bs_productions_scale-add",$scale_id,array("bs_productions_scale" => $scale));
        }else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
    
?>