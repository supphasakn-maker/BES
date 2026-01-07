<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_reserve","Add Reserve");
	$modal->initiForm("form_addreserve");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.reserve_silver.reserve.add()")
	));

	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");

	$blueprint = array(
		array(
			array(
				"type" =>"date",
				"name" => "lock_date",
				"caption" => "Lock Date",
				"value" => date("Y-m-d")
			)
		),
		array(
			array(
				"name" => "supplier_id",
				"caption" => "Supplier",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "bs_suppliers",
					"value" => "id",
					"name" => "name"
				)
			)
		),array(
			array(
				"name" => "weight_lock",
				"caption" => "Kilo Lock",
				"placeholder" => "Kilo to Lock"
			)
		),array(
			array(
				"name" => "discount",
				"caption" => "Discount",
				"placeholder" => "Locked Discount",
				"value" => "0.00"
			)
		),array(
			array(
				"name" => "type",
				"type" => "combobox",
				"caption" => "Type",
				"flex" => 2,
				"source" => array(
					array(1,"ใช้จริง"),
					array(2,"สำรอง")
				)
			)
		)
		
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>
