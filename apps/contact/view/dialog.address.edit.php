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
	
	$address = $dbc->GetRecord("os_address","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_edit_address","Edit Address");
	$modal->initiForm("form_editaddress");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.contact.address.edit()")
	));
	$modal->SetVariable(array(
		array("id",$address['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "address",
				"caption" => "Address",
				"placeholder" => "Address",
				"value" => $address['address']
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
				"caption" => "Country",
				"value" => $address['country']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"source" => array( 
					"table" => "db_cities",
					"value" => "id",
					"name" => "name",
					"where" => "country=".$address['country']
				),
				"flex" => 4,
				"name" => "city",
				
				"caption" => "Province",
				"value" => $address['city']
			),array(
				"type" => "comboboxdb",
				"source" => array( 
					"table" => "db_districts",
					"value" => "id",
					"name" => "name",
					"where" => "city=".$address['city']
				),
				"flex" => 4,
				"name" => "district",
				"caption" => "District",
				"value" => $address['district']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"flex" => 4,
				"source" => array( 
					"table" => "db_subdistricts",
					"value" => "id",
					"name" => "name",
					"where" => "district=".$address['district']
				),
				"name" => "subdistrict",
				"caption" => "Subdistrict",
				"value" => $address['subdistrict']
			),array(
				"flex" => 4,
				
				"name" => "postal",
				"caption" => "Postal",
				"value" => $address['postal']
			)
		),
		array(
			array(
				"name" => "remark",
				"caption" => "Remark",
				"placeholder" => "Remark Your Address",
				"value" => $address['remark']
			)
		)
			
		
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
