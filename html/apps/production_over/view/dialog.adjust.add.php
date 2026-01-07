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
$modal->setModel("dialog_add_adjust", "Add Adjust");
$modal->initiForm("form_addadjust");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_over.adjust.add()")
));

$blueprint = array(
    array(
        array(
            "type" => "comboboxdb",
            "name" => "type_id",
            "caption" => "Type",
            "source" => array(
                "table" => "bs_over_adjust_types",
                "name" => "CONCAT(name,' (',IF(type=1,'Include','Momo'),')')",
                "value" => "id"
            )
        )
    ),
    array(
        array(
            "name" => "code_no",
            "caption" => "เลขที่เอกสาร",
            "placeholder" => "เลขที่เอกสาร"
        )
    ),
    array(
        array(
            "type" => "comboboxdb",
            "name" => "product_id",
            "caption" => "Product",
            "source" => array(
                "table" => "bs_products",
                "name" => "name",
                "value" => "id"
            )
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "placeholder" => "Adjust Name",
            "flex" => 4,
            "value" => date("Y-m-d")
        ),
        array(
            "name" => "amount",
            "caption" => "Amount",
            "flex" => 4
        )

    ),
    array(
        array(
            "type" => "textarea",
            "name" => "remark",
            "caption" => "Remark",
            "placeholder" => "Remark"
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
