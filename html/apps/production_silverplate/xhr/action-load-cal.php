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
$sql = "SELECT * FROM bs_packing_items WHERE production_id = " . $_POST['id'];
$rst = $dbc->Query($sql);
while ($item = $dbc->Fetch($rst)) {

    $iterator = -1;
    for ($i = 0; $i < count($data); $i++) {

        $iterator = $i;
    }

    if ($iterator > -1) {
        $data[$iterator]['total']++;
        $data[$iterator]['weight'] += $item['weight_actual'];
    } else {
        array_push($data, array(
            "total" => 1,
            "weight" => $item['weight_actual'],

        ));
    }
}


echo json_encode(array(
    'success' => true,
    'data' => $data
));



$dbc->Close();
