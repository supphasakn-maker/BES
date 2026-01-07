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
	
	$organization = $dbc->GetRecord("os_organizations","*","id=".$_POST['id']);
	$address = $dbc->GetRecord("os_address","*","organization=".$organization['id']." AND priority=1");

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_edit_organization","Edit Organization");
	$modal->initiForm("form_editorganization","fn.app.contact.organization.edit()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.contact.organization.edit()")
	));
	$modal->SetVariable(array(
		array("org_id",$organization['id']),
		array("address_id",$address['id'])
	));
	
	
	$blueprint = array(
		array(
			array(
				"type" => "comboboxdb",
				"name" => "parent",
				"caption" => "Parent",
				"source" => array(
					"table" => "os_organizations",
					"name" => "name",
					"value" => "id"
				),
				"default" => array(
					"value" => "NULL",
					"name" => "Not Selected"
				),
				"value" => $organization['parent']
			)
		),array(
			array(
				"name" => "name",
				"caption" => "Name",
				"flex" => 6,
				"placeholder" => "Organization Name",
				"value" => $organization['name']
			),
			array(
				"name" => "branch",
				"caption" => "Branch",
				"placeholder" => "สาขาที่ต้องการระบุ",
				"flex-label" => 1,
				"flex" => 3,
				"value" => $organization['branch']
			)
		),
		array(
			array(
				"type" => "combobox",
				"name" => "type",
				"flex" => 4,
				"source" => array(
					"Public Company",
					"Limited Company",
					"Limited Partnership",
					"General Partnership",
					"Non-government Organization",
					"Union",
					"Other"
				),
				"caption" => "Type",
				"value" => $organization['type']
			),
			array(
				"name" => "tax_id",
				"flex" => 4,
				"caption" => "Tax ID",
				"placeholder" => "Tax ID",
				"value" => $organization['tax_id']
			)
		),array(
			array(
				"name" => "phone",
				"caption" => "Phone",
				"flex" => 4,
				"placeholder" => "Phone Number",
				"value" => $organization['phone']
			),array(
				"name" => "fax",
				"caption" => "Fax",
				"flex" => 4,
				"placeholder" => "Fax Number",
				"value" => $organization['fax']
			)
		),array(
			array(
				"name" => "email",
				"caption" => "E-Mail",
				"placeholder" => "E-Mail",
				"value" => $organization['email']
			)
		),array(
			array(
				"name" => "website",
				"caption" => "Website",
				"placeholder" => "Website",
				"value" => $organization['website']
			)
		),array(
			array(
				"name" => "address",
				"caption" => "Address",
				"placeholder" => "Address",
				"value" => $address['address']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"caption" => "Provice",
				"source" => array(
					"table" => "db_cities",
					"value" => "id",
					"name" => "name",
					"where" => "country=".$address['country']
				),
				"flex" => 4,
				"name" => "city",
				"value" => $address['city']
			),array(
				"type" => "comboboxdb",
				"flex" => 3,
				"source" => array(
					"table" => "db_districts",
					"value" => "id",
					"name" => "name",
					"where" => "city=".$address['city']
				),
				"name" => "district",
				"value" => $address['district']
			),array(
				"type" => "comboboxdb",
				"flex" => 3,
				"source" => array(
					"table" => "db_subdistricts",
					"value" => "id",
					"name" => "name",
					"where" => "district=".$address['district']
				),
				"name" => "subdistrict",
				"value" => $address['subdistrict']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"flex" => 6,
				"source" => array(
					"table" => "db_countries",
					"value" => "id",
					"name" => "name"
				),
				"name" => "country",
				"caption" => "Country",
				"value" => $address['country']
			),
			array(
				"flex" => 2,
				"name" => "postal",
				"caption" => "Postal",
				"value" => $address['postal']
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>