<?php
session_start();
include_once "../../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";
include_once "../../../../include/iface.php";
include_once "../../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_oven", "เพิ่มเตาอบ");
$modal->initiForm("form_add_oven");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_prepare.prepare.add_oven()")
));

$modal->SetVariable(array(
    array("id", $_POST['id'])
));


$blueprint = array(
    array(
        array(
            "type" => "combobox",
            "name" => "oven",
            "caption" => "เตาอบ",
            "source" => array("เตาอบ 1", "เตาอบ 2", "เตาอบ 3", "เตาอบ 4"),
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "round",
            "caption" => "รอบ",
            "readonly" => "readonly",
            "value" => $_REQUEST['id'],
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "date",
            "type" => "date",
            "caption" => "วันที่",
            "flex" => 4,
            "value" => date("Y-m-d")
        )
    ),
    array(
        array(
            "name" => "time_start",
            "type" => "time",
            "caption" => "เวลาเปิด",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "time_end",
            "type" => "time",
            "caption" => "เวลาปิด",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "temp",
            "type" => "number",
            "caption" => "อุณหภูมิ",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "remark",
            "type" => "textarea",
            "caption" => "หมายเหตุ",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "user",
            "type" => "comboboxdb",
            "source" => array(
                "table" => "bs_employees",
                "name" => "fullname",
                "value" => "fullname",
                "where" => "department = 3"
            ),
            "flex" => 4,
            "caption" => "พนักงาน"
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
