<?php
	session_start();
	include_once "../../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	include_once "../../../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_scale","เพิ่มเครื่องชั่ง");
	$modal->initiForm("form_add_scale");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.production_summarize.prepare.add_scale()")
	));
	
	$modal->SetVariable(array(
		array("id",$_POST['id'])
	));


	$blueprint = array(
        array(
			array(
				"type" => "combobox",
				"name" => "scale",
				"caption" => "เครื่องชั่ง",
				"source" => array("เครื่องชั่ง 1","เครื่องชั่ง 2"),
				"flex" => 4
			)
            ),array(
                array(
                    "name" => "round",
                    "caption" => "รอบ",
                    "readonly" => "readonly",
                    "value" => $_REQUEST['id'],
                    "flex" => 4
                )
            ),array(
                array(
                    "name" => "date",
                    "type" => "date",
                    "caption" => "วันที่",
                    "flex" => 4,
                    "value" => date("Y-m-d")
                )
            ),array(
                array(
                    "name" => "approve_scale",
                    "type" => "comboboxdb",
                    "source" => array( 
                        "table" => "bs_employees",
                        "name" => "fullname",
                        "value" => "fullname",										
                        "where" => "department = 3"
                    ),
                    "flex" => 4,
                    "caption" => "ผู้ชั่ง"
                )
            ),array(
                array(
                    "name" => "approve_packing",
                    "type" => "comboboxdb",
                    "source" => array( 
                        "table" => "bs_employees",
                        "name" => "fullname",
                        "value" => "fullname",										
                        "where" => "department = 3"
                    ),
                    "flex" => 4,
                    "caption" => "ผู้แพ็ค"
                )
            ),array(
                array(
                    "name" => "approve_check",
                    "type" => "comboboxdb",
                    "source" => array( 
                        "table" => "bs_employees",
                        "name" => "fullname",
                        "value" => "fullname",										
                        "where" => "department = 3"
                    ),
                    "flex" => 4,
                    "caption" => "ผู้เช็ค"
                )
            ),array(
                array(
                    "name" => "remark",
                    "type" => "textarea",
                    "caption" => "หมายเหตุ",
                    "flex" => 4
                )
            )
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>
