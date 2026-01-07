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


	if($dbc->HasRecord("bs_fonts_bwd","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Type Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
            '#status' => 1
			
		);

		if($dbc->Insert("bs_fonts_bwd",$data)){
			$font_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $font_id
			));

			$font = $dbc->GetRecord("bs_fonts_bwd","*","id=".$font_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"fonts-bwd-add",$font_id,array("fonts-bwd" => $font));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
