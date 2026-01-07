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


	if(!isset($_POST['name'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'There is no value'
		));
	}else{
		$data = array(
			"#id" => "DEFAULT",
			"#user" => $os->auth['id'],
			"#created" => "NOW()",
			"#updated" => "NOW()",
			"delivery_date" => $_POST['delivery_date_to'],
			"#info_amount" => $_POST['info_amount'],
			"info_mine" => $_POST['info_mine'],
			"#status_show" => $_POST['status_show'],
			"#prepare_date" => "NOW()",
			"#status" => 0,
			"#approved" => "NULL",
			"#amount" => $_POST['total'],
			"comment" => "",
		);

		if($dbc->Insert("bs_stock_prepare",$data)){
			$prepare_id = $dbc->GetID();
			
			for($i=0;$i<count($_POST['name']);$i++){
				$data = array(
					"#id" => "DEFAULT",
					"#prepare_id" => $prepare_id,
					"name" => $_POST['name'][$i],
					"#size" => $_POST['size'][$i],
					"#amount" => $_POST['amount'][$i],
					"comment" => $_POST['comment'][$i],
					"#status" => 0,
				);
				
				$dbc->Insert("bs_stock_items",$data);
			}
			
			echo json_encode(array(
				'success'=>true,
				'msg'=> $prepare_id
			));

			$prepare = $dbc->GetRecord("bs_stock_prepare","*","id=".$prepare_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"prepare-add",$prepare_id,array("prepares" => $prepare));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
