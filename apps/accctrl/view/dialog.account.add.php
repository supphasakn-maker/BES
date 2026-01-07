<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', 1);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$os->initial_lang("../lang");

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_account", $os->tr('account.dialog.add_header'));
$modal->initiForm("form_addaccount");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.accctrl.account.add()")
));
$modal->SetVariable(array(
	array("org_id", "")
));

$blueprint = array(
	array(
		array(
			"name" => "account",
			"caption" => $os->tr('account.dialog.name'),
			"placeholder" => "Account Name"
		)
	),
	array(
		array(
			"flex" => 2,
			"type" => "combobox",
			"name" => "option",
			"source" => array(
				array(1, "Database"),
				array(2, "New Company")
			),
			"caption" => $os->tr('account.dialog.type'),
			"placeholder" => "Account Name"
		),
		array(
			"flex" => 6,
			"name" => "org_name",
			"type" => "custom",
			"caption" => "Organization",
			"html" => '<div class="btn-group col-sm-12" role="group" aria-label="First group">
					<input name="org_name" class="form-control" readonly>
					<button type="button" class="btn btn-secondary"
						onclick="fn.app.contact.organization.dialog_lookup({callback:\'fn.app.accctrl.account.select_organization\'})">Select</button>
					</div>'
		)
	),
	"hr",
	array(
		"group" => "new_org",
		"items" => array(
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
			),
			array(
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
			),
			array(
				array(
					"name" => "phone",
					"caption" => "Phone",
					"flex" => 4,
					"placeholder" => "Phone Number"
				),
				array(
					"name" => "fax",
					"caption" => "Fax",
					"flex" => 4,
					"placeholder" => "Fax Number"
				)
			),
			array(
				array(
					"name" => "email",
					"caption" => "E-Mail",
					"placeholder" => "E-Mail"
				)
			),
			array(
				array(
					"name" => "website",
					"caption" => "Website",
					"placeholder" => "Website"
				)
			),
			array(
				array(
					"name" => "address",
					"caption" => "Address",
					"placeholder" => "Address"
				)
			),
			array(
				array(
					"caption" => "Provice",
					"type" => "combobox",
					"flex" => 4,
					"name" => "city",
				),
				array(
					"type" => "combobox",
					"flex" => 3,
					"name" => "district",
				),
				array(
					"type" => "combobox",
					"flex" => 3,
					"name" => "subdistrict",
				)
			),
			array(
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
		)
	),
);



$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
