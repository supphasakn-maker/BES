<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_customer", "Edit Customer");
$modal->initiForm("form_editcustomer");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Save Change", "fn.app.customer.customer.edit()")
));
$modal->SetVariable(array(
	array("id", $customer['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "name",
			"caption" => "ชื่อลุกค้า",
			"flex" => 10,
			"value" => $customer['name']
		)
	),
	array(

		array(
			"type" => "comboboxdb",
			"name" => "gid",
			"caption" => "Group",
			"source" => array(
				"table" => "bs_customer_groups",
				"name" => "name",
				"value" => "id"
			),
			"flex" => 5,
			"value" => $customer['gid']
		)
	),
	array(
		"type" => "tablist",
		"group" => "customer_tablist",
		"items" => array(
			array(
				"type" => "tab",
				"group" => "group_a",
				"name" => "Contact",
				"items" => array(
					array(
						array(
							"name" => "contact",
							"caption" => "ชื่อผู้ติดต่อ",
							"flex" => 10,
							"placeholder" => "Contact",
							"value" => $customer['contact']
						)
					),
					array(
						array(
							"name" => "phone",
							"caption" => "โทรศัพท์",
							"flex" => 4,
							"placeholder" => "Phone Number",
							"value" => $customer['phone']
						),
						array(
							"name" => "fax",
							"caption" => "แฟร์ก",
							"flex" => 4,
							"placeholder" => "Fax Number",
							"value" => $customer['fax']
						)
					),
					array(
						array(
							"name" => "email",
							"caption" => "อีเมลล์",
							"flex" => 10,
							"placeholder" => "E-Mail",
							"value" => $customer['email']
						)
					),
					array(
						array(
							"name" => "shipping_address",
							"type" => "textarea",
							"caption" => "ที่อยู่จัดส่ง",
							"placeholder" => "Address",
							"value" => $customer['shipping_address']
						)
					),
					array(
						array(
							"name" => "org_name",
							"caption" => "ชื่อบริษัท",
							"placeholder" => "Organization Name",
							"value" => $customer['org_name']
						)
					),
					array(
						array(
							"name" => "org_taxid",
							"caption" => "หมายเลขผู้เสียภาษี",
							"flex-label" => 3,
							"flex" => 4,
							"placeholder" => "",
							"value" => $customer['org_taxid']
						),
						array(
							"name" => "org_branch",
							"caption" => "สาขา",
							"flex-label" => 1,
							"flex" => 4,
							"placeholder" => "ระบุสาขา",
							"value" => $customer['org_branch']
						)
					),
					array(
						array(
							"name" => "billing_address",
							"type" => "textarea",
							"flex-label" => 3,
							"flex" => 9,
							"caption" => "ที่อยู่ใบกำกับภาษี",
							"placeholder" => "Address",
							"value" => $customer['billing_address']
						)
					)
				)
			),
			array(
				"type" => "tab",
				"group" => "group_b",
				"name" => "รายละเอียด",
				"items" => array(
					array(
						array(
							"name" => "remark",
							"caption" => "Remark",
							"type" => "textarea",
							"placeholder" => "",
							"value" => $customer['remark']
						)
					),
					array(
						array(
							"name" => "comment",
							"caption" => "Comment",
							"type" => "textarea",
							"placeholder" => "",
							"value" => $customer['comment']
						)
					),
					array(
						array(
							"type" => "combobox",
							"name" => "new_cus",
							"flex" => 5,
							"value" => "0",
							"source" => array(
								array("ลูกค้าเก่า", "ไม่ระบุ"),
								array("ลูกค้าเก่า", "ลูกค้าเก่า"),
								array("ลูกค้าใหม่", "ลูกค้าใหม่")
							),
							"caption" => "ประเภทลูกค้า",
							"value" => $customer['new_cus']
						),
						array(
							"type" => "combobox",
							"name" => "date_newcus",
							"flex" => 3,
							"source" => array(
								array("0", "ไม่ระบุ"),
								array("2021", "2021"),
								array("2022", "2022"),
								array("2023", "2023"),
								array("2024", "2024"),
								array("2025", "2025")
							),
							"caption" => "ปี",
							"value" => $customer['date_newcus']
						)
					),
					array(
						array(
							"type" => "comboboxdb",
							"name" => "default_sales",
							"caption" => "Sale",
							"source" => array(
								"table" => "bs_employees",
								"name" => "fullname",
								"value" => "id"
							),
							"default" => array(
								"value" => "NULL",
								"name" => "Not Selected"
							),
							"value" => $customer['default_sales']
						)
					),
					array(
						array(
							"type" => "comboboxdatabank",
							"source" => "db_bank",
							"name" => "default_bank",
							"caption" => "ธนาคาร",
							"default" => array(
								"value" => "none",
								"name" => "ไม่ระบุ"
							),
							"flex" => 6,
							"placeholder" => "Bank Detail",
							"value" => $customer['default_bank']
						),
						array(
							"type" => "combobox",
							"name" => "default_vat_type",
							"flex" => 2,
							"value" => "0%",
							"source" => array(
								array(0, "0%"),
								array(2, "7%")
							),
							"caption" => "ภาษีมูลค่าเพิ่ม",
							"value" => $customer['default_vat_type']
						)
					),
					array(
						array(
							"type" => "comboboxdatabank",
							"source" => "db_payment",
							"name" => "default_payment",
							"caption" => "เงือนไขการชำระเงิน",
							"default" => array(
								"value" => "none",
								"name" => "ไม่ระบุ"
							),
							"flex-label" => 3,
							"flex" => 6,
							"value" => $customer['default_payment']
						)
					),
					array(
						array(
							"type" => "comboboxdb",
							"source" => array(
								"table" => "bs_products",
								"name" => "name",
								"value" => "id",
								"where" => "id != 9"
							),
							"name" => "default_product",
							"caption" => "สินค้า",
							"default" => array(
								"value" => "none",
								"name" => "ไม่ระบุ"
							),
							"flex-label" => 3,
							"flex" => 6,
							//"value" => $customer['default_payment']
						)
					)
				)
			),
			array(
				"type" => "tab",
				"group" => "group_c",
				"name" => "ข้อมูลการแบ่ง Pack",
				"items" => array(
					array(
						array(
							"name" => "default_pack",
							"caption" => "Default Pack",
							"type" => "textarea",
							"rows" => 10,
							"placeholder" => "",
							"value" => $customer['default_pack']
						)
					)
				)
			),
			array(
				"type" => "tab",
				"group" => "group_d",
				"name" => "COA",
				"items" => array(
					array(
						array(
							"type" => "combobox",
							"name" => "coa",
							"caption" => "ต้องการ COA BRAND",
							"flex-label" => 3,
							"flex" => 9,
							"source" => array(
								array(0, "ไม่ต้องการ"),
								array(1, "ต้องการ")
							),
							"flex" => 6,
							"value" => $customer['coa']
						)
					),
					array(
						array(
							"name" => "po",
							"caption" => "PO",
							"placeholder" => "PO",
							"value" => $customer['po']
						)
					),
					array(
						array(
							"type" => "date",
							"name" => "date_po",
							"caption" => "วันที่เปิด PO",
							"placeholder" => "วันที่เปิด PO",
							"value" => $customer['date_po']
						)
					)
				)
			),
			array(
				"type" => "tab",
				"group" => "group_e",
				"name" => "COC",
				"items" => array(
					array(
						array(
							"name" => "contact_coc",
							"caption" => "Receiver",
							"flex" => 10,
							"placeholder" => "Receiver COC",
							"value" => $customer['contact_coc']
						)
					),
					array(
						array(
							"name" => "org_name_coc",
							"caption" => "Name Of Company",
							"placeholder" => "Company Name",
							"value" => $customer['org_name_coc']
						)
					),
					array(
						array(
							"name" => "address_coc",
							"type" => "textarea",
							"caption" => "Address",
							"placeholder" => "Address",
							"value" => $customer['address_coc']
						)
					),
					array(
						array(
							"name" => "certificate_number",
							"caption" => "Certificate Number",
							"placeholder" => "Certificate Number",
							"value" => $customer['certificate_number']
						)
					),
					array(
						array(
							"name" => "certificate_coc",
							"caption" => "Certificate Date",
							"placeholder" => "Certificate Start End",
							"value" => $customer['certificate_coc']
						)
					)
				)
			),
			array(
				"type" => "tab",
				"group" => "group_f",
				"name" => "SILVER NOW",
				"items" => array(
					array(
						array(
							"name" => "silvernow_no",
							"caption" => "เบอร์สมัคร APP",
							"flex" => 10,
							"placeholder" => "เบอร์สมัคร APP",
							"value" => $customer['silvernow_no']
						)
					)
				)
			),
			array(
				"type" => "tab",
				"group" => "group_g",
				"name" => "ใบคำร้อง",
				"items" => array(
					array(
						array(
							"type" => "combobox",
							"name" => "export",
							"flex-label" => 3,
							"flex" => 9,
							"source" => array(
								array("ลาดกระบัง", "ลาดกระบัง"),
								array("อัญธานี", "อัญธานี")
							),
							"value" => $customer['export']
						)
					)
				)
			),
			array(
				"type" => "tab",
				"group" => "group_h",
				"name" => "ลายเซ็นกรมศุล",
				"items" => array(
					array(
						array(
							"type" => "combobox",
							"name" => "signature",
							"flex-label" => 3,
							"flex" => 9,
							"source" => array(
								array("นายบวร กิตติเวทางค์", "นายบวร กิตติเวทางค์"),
								array("นางวิลาสินี ศานติจารี", "นางวิลาสินี ศานติจารี"),
								array("น.ส.อัมรา นาคเอี่ยม", "น.ส.อัมรา นาคเอี่ยม"),
								array("น.ส.คนึงนิจ เย็นขัน", "น.ส.คนึงนิจ เย็นขัน "),
								array("น.ส.สุพรรณี ลีแก้ว",  "น.ส.สุพรรณี ลีแก้ว")
							),
							"value" => $customer['signature']
						)
					)
				)
			)
		)
	)
);



$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
