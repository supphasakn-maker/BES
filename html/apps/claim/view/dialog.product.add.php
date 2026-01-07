<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_product", "Add Product");
$modal->initiForm("form_addproduct");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.claim.product.add()")
));

$blueprint = array(
	array(
		array(
			"name" => "type",
			"type" => "combobox",
			"caption" => "ประเภทการแจ้ง",
			"source" => array('แจ้งเพื่อทราบและปรับปรุง', 'แจ้งเพื่อเคลมสินค้า')
		)
	),
	array(
		array(
			"name" => "order_id",
			"type" => "comboboxdb",
			"caption" => "หมายเลข Order",
			"source" => array(
				"table" => "bs_orders",
				"name" => "code",
				"value" => "id",
				"where" => "DATE(date) > '2025-01-01'"
			)
		)
	),
	array(
		array(
			"name" => "org_name",
			"caption" => "บริษัท"
		)
	),
	array(
		array(
			"name" => "product_id",
			"caption" => "Product",
			"readonly" => "readonly"
		)
	),
	array(
		array(
			"name" => "contact_issuer",
			"caption" => "ผู้แจ้ง",
			"placeholder" => "ผู้แจ้ง",
			"flex" => 3
		),
		array(
			"name" => "contact_sender",
			"placeholder" => "ผู้ส่ง",
			"flex" => 3
		),
		array(
			"name" => "contact_sales",
			"placeholder" => "พนักงานขาย",
			"flex" => 3
		)
	),
	array(
		array(
			"name" => "issue",
			"type" => "combobox",
			"caption" => "ประเภทการแจ้ง",
			"source" => array('เป็นผง', 'เม็ดเหลือง/ไม่สวย', 'แพ็คเก็จไม่สมบูรณ์', 'น้ำหนักขาด', 'ความชื้น')
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date_claim",
			"caption" => "วันที่รับแจ้ง",
			"value" => date("Y-m-d"),
			"flex" => 4
		),
		array(
			"name" => "amount",
			"caption" => "จำนวนปัญหา",
			"placeholder" => "จำนวนปัญหา",
			"flex" => 4
		)
	),
	array(
		array(
			"name" => "pack_problem",
			"caption" => "ขนาดถุง",
			"placeholder" => "ใส่ขนาดถุงที่พบปัญหา"
		)
	),
	array(
		array(
			"name" => "pack_claim",
			"caption" => "จำนวนการเคลม",
			"placeholder" => "ใส่จำนวนที่ต้องการเคลมสินค้า"
		)
	),
	array(
		array(
			"name" => "detail",
			"type" => "textarea",
			"caption" => "รายละเอียด",
			"placeholder" => "รายละเอียด"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
