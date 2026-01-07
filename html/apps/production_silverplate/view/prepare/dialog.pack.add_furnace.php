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
$modal->setModel("dialog_add_furnace", "เพิ่มเตาหลอม");
$modal->initiForm("form_add_furnace");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_silverplate.prepare.add_furnace()")
));

$modal->SetVariable(array(
    array("id", $_POST['id'])
));


$blueprint = array(
    array(
        array(
            "type" => "combobox",
            "name" => "furnace",
            "caption" => "เตาหลอม",
            "source" => array("เตาหลอม 1", "เตาหลอม 2"),
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "round",
            "caption" => "รอบ",
            "value" => $_REQUEST['id'],
            "readonly" => "readonly",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "crucible",
            "type" => "comboboxdb",
            "caption" => "เลขเบ้า",
            "source" => array(
                "name" => "round",
                "value" => "round",
                "table" => "bs_productions_crucible",
                "where" => "status = 0"
            )
        )
    ),
    array(
        array(
            "name" => "amount",
            "type" => "number",
            "caption" => "จำนวน",
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
