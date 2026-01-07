<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";
include_once "../../../include/datastore.php";

$dbc = new dbc;
$dbc = new datastore;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_in", "เพิ่ม");
$modal->initiForm("form_addin");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_pmr.in.add()")
));

$blueprint = array(
    array(
        array(
            "name" => "round",
            "type" => "comboboxdb",
            "caption" => "Lots",
            "source" => array(
                "table" => "bs_productions",
                "value" => "round",
                "name" => "round",
                "where" => "created > '2025-06-01' AND round_summary = 0 "
            ),
            "autoselect" => true
        )
    ),
    array(
        array(
            "name" => "product_id",
            "type" => "comboboxdb",
            "caption" => "Products",
            "source" => array(
                "table" => "bs_products",
                "value" => "id",
                "name" => "name",
                "where" => "id != 9"
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
