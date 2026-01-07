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
$modal->setModel("dialog_add_scrap", "เพิ่มหมายเลข");
$modal->initiForm("form_addscrap");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.stock_silver.scrap.add()")
));

$aPacking = json_decode($os->load_variable("aPacking", "json"), true);

$select_packtype = '<select name="pack_name" class="form-control mr-2">';
foreach ($aPacking as $pack) {
    $readonly = isset($pack['readonly']) ? $pack['readonly'] : true;
    $select_packtype .=  '<option data-value="' . $pack['value'] . '" data-readonly="' . ($readonly ? "true" : "false") . '">' . $pack['name'] . '</option>';
}
$select_packtype .= '</select>';

$blueprint = array(
    array(
        array(
            "type" => "comboboxdb",
            "name" => "product_id",
            "caption" => "Product",
            "source" => array(
                "name" => "name",
                "value" => "id",
                "table" => "bs_products",
                "where" => "status = 1"
            )
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "วันที่รับเข้า",
            "autocomplete" => "off",
            "value" => date("Y-m-d"),
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "code",
            "caption" => "หมายเลขแท่ง",
            "autocomplete" => "off",
            "aria-autocomplete" => "none",
            "flex" => 4
        )
    ),
    array(
        array(
            "type" => "custom",
            "caption" => "ชื่อถุง",
            "html" => $select_packtype,
        )
    ),
    array(
        array(
            "type" => "combobox",
            "name" => "pack_type",
            "caption" => "ประเภทถุง",
            "source" => array("ถุงปกติ", "ถุงกระสอบ", "แท่ง"),
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "weight_expected",
            "type" => "number",
            "caption" => "น้ำหนัก",
            "value" => "1",
            "flex" => 4
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
