<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$data = array();

$switch_id = $_POST['id'];
$switch = $dbc->GetRecord("bs_productions_switch", "*", "id=" . $switch_id);

$total = 0;
$sql = "SELECT
		bs_packing_items.weight_actual as weight_actual
	FROM bs_packing_items 
	LEFT JOIN bs_switch_pack_items ON bs_packing_items.id= bs_switch_pack_items.item_id
	WHERE bs_switch_pack_items.switch_id = " . $switch_id;
$rst = $dbc->Query($sql);
while ($item = $dbc->Fetch($rst)) {
    $total += $item['weight_actual'];
}

$remain = $switch['weight_out_packing'] - $total;
echo json_encode(array(
    "order" => $data,
    "delivery" => $switch,
    "total" => number_format($total, 4),
    "remain" => number_format($remain, 4)

));

$dbc->Close();
