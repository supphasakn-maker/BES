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
$modal->setModel("dialog_add_recycle", "เพิ่ม");
$modal->initiForm("form_addrecycle");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_pmr.recycle.add()")
));

$blueprint = array(
    array(
        array(
            "name" => "product_id",
            "type" => "comboboxdb",
            "caption" => "Products",
            "source" => array(
                "table" => "bs_products",
                "value" => "id",
                "name" => "name",
                "where" => "id = 4"

            )
        )
    ),
    array(
        array(
            "name" => "weight_out_total",
            "type" => "number",
            "caption" => "น้ำหนัก",
            "flex" => 6
        )
    ),
    array(
        array(
            "name" => "remark",
            "type" => "textarea",
            "caption" => "หมายเหตุ",
            "flex" => 6
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
