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
	$spot = $dbc->GetRecord("bs_sales_spot","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_spot","Edit Spot");
	$modal->initiForm("form_editspot");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sales.spot.edit()")
	));
	$modal->SetVariable(array(
		array("id",$spot['id'])
	));
	
	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");
	
	$blueprint = array(
		array(
				array(
				"name" => "supplier_id",
				"caption" => "Supplier",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "bs_suppliers",
					"value" => "id",
					"name" => "name"
				),
				"value" => $spot['supplier_id']
			)
		),array(
			array(
				"name" => "type",
				"type" => "combobox",
				"caption" => "Type",
				"source" => array(
					array("physical","Physical"),
					array("stock","Stock"),
					array("trade","Trade"),
					array("defer","Defer")
				),
				"value" => $spot['type']
			)
		),array(
			array(
				"name" => "amount",
				"caption" => "Amount",
				"placeholder" => "Amount To Sales",
				"value" => $spot['amount']
			)
		),array(
			array(
				"name" => "rate_spot",
				"caption" => "Spot",
				"placeholder" => "Spot Name",
				"flex" => 4,
				"value" => $spot['rate_spot']
			),array(
				"name" => "rate_pmdc",
				"caption" => "Pm/Dc",
				"placeholder" => "premium/discount",
				"flex" => 4,
				"value" => $spot['rate_pmdc']
			)
		),array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"placeholder" => "Sales Date",
				"value" => $spot['date']
			)
		),array(
			array(
				"type" => "date",
				"name" => "value_date",
				"caption" => "Value Date",
				"value" => $spot['value_date']
			)
		),array(
			array(
				"name" => "method",
				"type" => "combobox",
				"caption" => "Method",
				"flex" => 2,
				"source" => array(
					"Call To Buy",
					"Deal ID",
					"Via Message"
				),
				"value" => $spot['method']
			),array(
				"name" => "ref",
				"caption" => "Reference",
				"flex" => 6,
				"placeholder" => "Reference",
				"value" => $spot['ref']
			)
		),array(
			array(
				"type" => "comboboxdatabank",
				"source" => "db_bank",
				"name" => "bank",
				"caption" => "ธนาคาร",
				"default" => array(
					"value" => "none",
					"name" => "ไม่ระบุ"
				),
				"placeholder" => "Bank Detail",
				"value" => $spot['bank']
			)
		
		),array(
			array(
				"type" => "textarea",
				"name" => "comment",
				"caption" => "Comment",
				"placeholder" => "Comment",
				"value" => $spot['comment']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
