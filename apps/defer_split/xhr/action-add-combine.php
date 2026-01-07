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
	
	
	if($_POST['select_import']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please Select Import'
		));
	}else{
		
		$data = array(
			"#id" => "DEFAULT",
			//"#transfer_id" => $_POST['transfer_id'],
			//"#amount" => $_POST['amount'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"remark" => addslashes($_POST['remark'])
		);
		
		
		if($dbc->Insert("bs_import_combine",$data)){
			$combine_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $combine_id
			));
			
			
			$total = 0;
			$select_import = explode(",",$_POST['select_import']);
			for($i=0;$i<count($select_import);$i++){
				$import = $dbc->GetRecord("bs_imports","*","id=".$select_import[$i]);
				$dbc->Update("bs_imports",array(
					"#combine_id" => $combine_id
				),"id = ".$select_import[$i]);
				$total += $import['amount'];
				
			}
			$dbc->Update("bs_import_combine",array(
				"#amount" => $total
			),"id = ".$combine_id);
			
			
			
			$combine = $dbc->GetRecord("bs_import_combine","*","id=".$combine_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"import-add",$combine,array("bs_combine" => $combine));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>