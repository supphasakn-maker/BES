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
	$adjust = $dbc->GetRecord("bs_stock_adjusted","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_adjust","Edit Adjust");
	$modal->initiForm("form_editadjust");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.stock.adjust.edit()")
	));
	$modal->SetVariable(array(
		array("id",$adjust['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"type" => "comboboxdb",
				"name" => "type_id",
				"caption" => "Type",
				"source" => array(
					"table" => "bs_stock_adjuest_types",
					"name" => "CONCAT(name,' (',IF(type=1,'Include','Momo'),')')",
					"value" => "id"
				),
				"value" => $adjust['type_id']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"name" => "product_id",
				"caption" => "Product",
				"source" => array(
					"table" => "bs_products",
					"name" => "name",
					"value" => "id"
				),
				"value" => $adjust['product_id']
			)
		),array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Name",
				"placeholder" => "Adjust Name",
				"flex" => 4,
				"value" => $adjust['date']
			),
			array(
				"name" => "amount",
				"caption" => "Amount",
				"flex" => 4,
				"value" => $adjust['amount']
			)
		
		),array(
			array(
				"type" => "textarea",
				"name" => "remark",
				"caption" => "Remark",
				"placeholder" => "Remark",
				"value" => $adjust['remark']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
