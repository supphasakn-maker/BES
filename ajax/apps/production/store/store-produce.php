<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_productions.id",
		"round" => "bs_productions.round",
		"created" => "bs_productions.created",
		"updated" => "bs_productions.updated",
		"user" => "bs_productions.user",
		"remark" => "bs_productions.remark",
		"weight_in_safe" => "bs_productions.weight_in_safe",
		"weight_in_plate" => "bs_productions.weight_in_plate",
		"weight_in_nugget" => "bs_productions.weight_in_nugget",
		"weight_in_blacknugget" => "bs_productions.weight_in_blacknugget",
		"weight_in_whitedust" => "bs_productions.weight_in_whitedust",
		"weight_in_blackdust" => "bs_productions.weight_in_blackdust",
		"weight_in_refine" => "bs_productions.weight_in_refine",
		"weight_in_1" => "bs_productions.weight_in_1",
		"weight_in_2" => "bs_productions.weight_in_2",
		"weight_in_3" => "bs_productions.weight_in_3",
		"weight_in_4" => "bs_productions.weight_in_4",
		"weight_in_total" => "bs_productions.weight_in_total",
		"weight_out_safe" => "bs_productions.weight_out_safe",
		"weight_out_plate" => "bs_productions.weight_out_plate",
		"weight_out_nugget" => "bs_productions.weight_out_nugget",
		"weight_out_blacknugget" => "bs_productions.weight_out_blacknugget",
		"weight_out_whitedust" => "bs_productions.weight_out_whitedust",
		"weight_out_blackdust" => "bs_productions.weight_out_blackdust",
		"weight_out_refine" => "bs_productions.weight_out_refine",
		"weight_out_packing" => "bs_productions.weight_out_packing",
		"weight_out_total" => "bs_productions.weight_out_total",
		"weight_margin" => "bs_productions.weight_margin",
		"submited" => "bs_productions.submited",
		"delivery_license" => "bs_productions.delivery_license",
		"delivery_driver" => "bs_productions.delivery_driver",
		"delivery_time" => "bs_productions.delivery_time",
		"approver_appointment" => "bs_productions.approver_appointment",
		"approver_weight" => "bs_productions.approver_weight",
		"approver_general" => "bs_productions.approver_general",
		"type_material" => "bs_productions.type_material",
		"type_work" => "bs_productions.type_work",
		"type_thaicustoms_method" => "bs_productions.type_thaicustoms_method",
		"status" => "bs_productions.status",
		"import_date" => "bs_productions.import_date",
		"import_weight_in" => "bs_productions.import_weight_in",
		"import_weight_actual" => "bs_productions.import_weight_actual",
		"import_weight_margin" => "bs_productions.import_weight_margin",
		"import_bar" => "bs_productions.import_bar",
		"import_bar_weight" => "bs_productions.import_bar_weight",
	);
	
	$where = "bs_productions.status > 0";
	
	if(isset($_GET['date_from'])){
		$where .= " AND DATE(bs_productions.submited) BETWEEN '".$_GET['date_from']."' AND '".$_GET['date_to']."'";
	}

	$table = array(
		"index" => "id",
		"name" => "bs_productions",
		"where" => $where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
