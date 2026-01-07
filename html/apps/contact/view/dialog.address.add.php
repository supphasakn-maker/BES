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
	$modal->setModel("dialog_add_address","Add Address");
	$modal->initiForm("form_addaddress");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.contact.address.add()")
	));
	$modal->SetVariable(array(
		array("type",$_POST['type']),
		array("id",$_POST['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "address",
				"caption" => "Address",
				"placeholder" => "Address"
			)
		),
		array(
			array(
				"type" => "comboboxdb",
				"source" => array( 
					"table" => "db_countries",
					"value" => "id",
					"name" => "name"
				),
				"name" => "country",
				"caption" => "Country"
			)
		),array(
			array(
				"type" => "combobox",
				"flex" => 4,
				"name" => "city",
				"caption" => "Province"
			),array(
				"type" => "combobox",
				"flex" => 4,
				"name" => "district",
				"caption" => "District"
			)
		),array(
			array(
				"type" => "combobox",
				"flex" => 4,
				"name" => "subdistrict",
				"caption" => "Subdistrict"
			),array(
				"flex" => 4,
				"name" => "postal",
				"caption" => "Postal"
			)
		),
		array(
			array(
				"name" => "remark",
				"caption" => "Remark",
				"placeholder" => "Remark Your Address"
			)
		)
		
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
