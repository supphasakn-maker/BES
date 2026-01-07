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
$modal->setModel("dialog_add_switch", "ADD LOT");
$modal->initiForm("form_addswitch");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_switch.switch.add()")
));

$blueprint = array(
    array(
        array(
            "name" => "round_id",
            "type" => "comboboxdb",
            "caption" => "เลือก LOT ที่ยืม",
            "source" => array(
                "table" => "bs_productions",
                "value" => "id",
                "name" => "round",
                "where" => "round_summary = 0 AND YEAR(created) > 2024"

            )
        )
    ),
    array(
        array(
            "name" => "import_lot",
            "caption" => "LOT ที่ยืม",
            "placeholder" => "Round Number"
        ),
    ),
    array(
        array(
            "name" => "round_turn",
            "type" => "comboboxdb",
            "caption" => "เลือก LOT ที่คืน",
            "source" => array(
                "table" => "bs_productions_round",
                "value" => "id",
                "name" => "import_lot",
                "where" => "status = 1"
            )
        )
    ),
    array(
        array(
            "name" => "round_turn_id",
            "caption" => "LOT ที่ต้องคืน",
            "placeholder" => "Round Number"
        ),
    ),

    array(
        array(
            "name" => "amount",
            "caption" => "จำนวนที่ยืม",
            "placeholder" => "amount"
        ),
    ),
    array(
        array(
            "name" => "amount_balance",
            "caption" => "จำนวน",
            "placeholder" => "amount",
            "readonly" => "readonly"
        ),
    ),
    array(
        array(
            "name" => "balance",
            "caption" => "จำนวนคงเหลือ",
            "placeholder" => "balance",
            "readonly" => "readonly"
        ),
    ),

    array(
        array(
            "name" => "product_type_id",
            "caption" => "สินค้า",
            "placeholder" => "สินค้า",
            "readonly" => "readonly"
        ),
    ),
    array(
        array(
            "name" => "product_id_turn",
            "caption" => "สินค้าที่ยืม",
            "placeholder" => "สินค้า",
            "readonly" => "readonly"
        ),
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
