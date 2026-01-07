<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			$map_item = $dbc->GetRecord("bs_mapping_usd_purchases","*","id=".$this->param['id']);
			$sql = "SELECT * FROM bs_mapping_usd_purchases WHERE purchase_id = ".$map_item['purchase_id'];
			$rst = $dbc->Query($sql);
			while($item = $dbc->Fetch($rst)){
				echo '<div class="li">';
				echo $item['id'];
				echo '|';
				echo '<span class="badge badge-danger">'.$item['mapping_id'].'</span>';
				echo '</div>';
			}
			echo '<pre>';
			//var_dump($map_item);
			echo '</pre>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_lookup_usd","Lookup Mapping");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
