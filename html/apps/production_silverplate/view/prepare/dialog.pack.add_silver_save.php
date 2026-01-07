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
$modal->setModel("dialog_add_silver_save", "เพิ่ม");
$modal->initiForm("form_add_silver_save");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_silverplate.prepare.add_silver_save()")
));

$modal->SetVariable(array(
    array("id", $_POST['id'])
));


$blueprint = array(
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
            "name" => "bar",
            "type" => "number",
            "caption" => "จำนวนแท่งเงิน",
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "amount",
            "type" => "number",
            "caption" => "น้ำหนัก",
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
            "name" => "time",
            "type" => "time",
            "caption" => "เวลา",
            "flex" => 4
        )
    ),
    array(
        array(
            "type" => "combobox",
            "name" => "user",
            "caption" => "ชื่อผู้ปิด Save",
            "source" => array("คุณอานัส วรกาญจน์", "คุณอุรุสญา มหากนก", "คุณณัฐวุฒิ กฤษดานนท์"),
            "flex" => 4
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
