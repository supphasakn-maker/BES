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
		if($dbc->HasRecord("bs_orders_bwd","id=".$item)){
			$order = $dbc->GetRecord("bs_orders_bwd","*","id=".$item);
			if(!is_null($order['parent'])){
				$deletable = true;
				$aDelete = array();
				$sql = "SELECT * FROM bs_orders_bwd WHERE parent = ".$order['parent']." AND id != ".$item;
				$rst = $dbc->Query($sql);
				while($line =$dbc->Fetch($rst)){
					if($dbc->HasRecord("bs_orders_bwd","parent=".$line['id'])){
						$deletable = false;
					}
					array_push($aDelete,$line['id']);
				}
				
				if($deletable){
					foreach($aDelete as $id){
						$dbc->Delete("bs_orders_bwd","id=".$id);
					}
					$dbc->Delete("bs_orders_bwd","id=".$item);
					$dbc->Update("bs_orders_bwd",array('#status' => 1,'#updated' => 'NOW()'),"id=".$order['parent']);
				}
			}else{
				$dbc->Delete("bs_orders_bwd","id=".$item);
				$os->save_log(0,$_SESSION['auth']['user_id'],"order-bwd-delete",$item,array("bs_orders_bwd" => $order));
			}
		}
		
		
		
		
	}

	$dbc->Close();
?>
