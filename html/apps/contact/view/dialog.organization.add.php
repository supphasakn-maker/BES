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
	$modal->setModel("dialog_add_organization","Add Organization");
	$modal->initiForm("form_addorganization","fn.app.contact.organization.add()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.contact.organization.add()")
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
				)
			)
		),array(
			array(
				"name" => "name",
				"caption" => "Name",
				"flex" => 6,
				"placeholder" => "Organization Name"
			),
			array(
				"name" => "branch",
				"caption" => "Branch",
				"placeholder" => "สาขาที่ต้องการระบุ",
				"flex-label" => 1,
				"flex" => 3,
				"value" => "สำนักงานใหญ่"
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
				"caption" => "Type"
			),
			array(
				"name" => "tax_id",
				"flex" => 4,
				"caption" => "Tax ID",
				"placeholder" => "Tax ID"
			)
		),array(
			array(
				"name" => "phone",
				"caption" => "Phone",
				"flex" => 4,
				"placeholder" => "Phone Number"
			),array(
				"name" => "fax",
				"caption" => "Fax",
				"flex" => 4,
				"placeholder" => "Fax Number"
			)
		),array(
			array(
				"name" => "email",
				"caption" => "E-Mail",
				"placeholder" => "E-Mail"
			)
		),array(
			array(
				"name" => "website",
				"caption" => "Website",
				"placeholder" => "Website"
			)
		),array(
			array(
				"name" => "address",
				"caption" => "Address",
				"placeholder" => "Address"
			)
		),array(
			array(
				"caption" => "Provice",
				"type" => "combobox",
				"flex" => 4,
				"name" => "city",
			),array(
				"type" => "combobox",
				"flex" => 3,
				"name" => "district",
			),array(
				"type" => "combobox",
				"flex" => 3,
				"name" => "subdistrict",
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
				"caption" => "Country"
			),
			array(
				"flex" => 2,
				"name" => "postal",
				"caption" => "Postal"
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>