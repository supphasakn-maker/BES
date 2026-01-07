<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
		$production = $dbc->GetRecord("bs_productions", "*", "id=" . $this->param['id']);

		$sql = "SELECT 
			
					bs_packing_items.id AS id ,
					bs_packing_items.code AS code, 
					bs_packing_items.pack_name AS pack_name, 
					bs_packing_items.pack_type AS pack_type,
					bs_packing_items.weight_actual AS weight_actual,
					bs_packing_items.weight_expected AS weight_expected,
					bs_packing_items.parent AS parent,
					bs_packing_items.status AS status,
					bs_packing_items.created AS created
					
				FROM bs_packing_items LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
				WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.production_id = " . $production['id'] . "
				AND bs_packing_items.status > -1
			";
		$rst = $dbc->Query($sql);
		while ($item = $dbc->Fetch($rst)) {
			echo '<div class="badge badge-warning mr-1">' . $item['code'] . '</div>';
		}
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_delivery_remain", "Delivery Delivery");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
));
$modal->EchoInterface();

$dbc->Close();
