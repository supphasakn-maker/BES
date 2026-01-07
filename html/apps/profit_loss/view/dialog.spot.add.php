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
$modal->setModel("dialog_add_spot", "Add Spot");
$modal->initiForm("form_addspot");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.profit_loss.profitloss.add_spot()")
));

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");
$rate_pmdc_purchase = $os->load_variable("rate_pmdc_purchase");

$date_filter = isset($_POST['date_filter']) ? $_POST['date_filter'] : date("Y-m-d"); // Default เป็นวันปัจจุบัน

$blueprint = array(
    array(
        array(
            "name" => "supplier_id",
            "caption" => "Supplier",
            "type" => "comboboxdb",
            "source" => array(
                "table" => "bs_suppliers",
                "value" => "id",
                "name" => "name",
                "where" => "status = 1"
            )
        )
    ),
    array(
        array(
            "name" => "type",
            "type" => "combobox",
            "caption" => "Type",
            "source" => array(
                array("physical", "Physical"),
                array("MTM", "Mark to Market"),
            )
        )
    ),
    array(
        array(
            "name" => "amount",
            "caption" => "Amount",
            "placeholder" => "กิโลที่ซื้อ",
            "flex" => 6
        ),
        array(
            "name" => "currency",
            "type" => "combobox",
            "source" => array(
                "USD",
                "THB"
            ),
            "flex" => 4
        )
    ),
    array(
        array(
            "name" => "THBValue",
            "class" => "text-right pr-4",
            "placeholder" => "0.0000",
            "flex" => 6
        )
    ),
    array(
        array(
            "name" => "rate_spot",
            "caption" => "Spot",
            "placeholder" => "Spot Name",
            "flex" => 4,
            "value" => $rate_spot
        ),
        array(
            "name" => "rate_pmdc",
            "caption" => "Pm/Dc",
            "placeholder" => "premium/discount",
            "flex" => 4,
            "value" => number_format($rate_pmdc_purchase, 4)
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "placeholder" => "Purchase Date",
            "value" => date("Y-m-d")
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "value_date",
            "caption" => "Value Date",
            "placeholder" => "Value Date",
            "readonly" => "readonly",
            "value" => $date_filter
        )
    ),
    array(
        array(
            "name" => "noted",
            "type" => "combobox",
            "caption" => "Noted",
            "source" => array(
                "Normal",
                "Close-Adjust",
                "Open-Adjust"
            )
        )
    ),
    array(
        array(
            "name" => "method",
            "type" => "combobox",
            "caption" => "Method",
            "flex" => 2,
            "source" => array(
                "Call To Buy",
                "Deal ID",
                "Via Message"
            )
        ),
        array(
            "name" => "ref",
            "caption" => "Reference",
            "flex" => 6,
            "placeholder" => "Reference"
        )
    ),
    array(
        array(
            "name" => "adj_supplier",
            "caption" => "ADJ Supplier",
            "type" => "comboboxdb",
            "source" => array(
                "table" => "bs_suppliers",
                "value" => "id",
                "name" => "name",
                "where" => "status = 1"
            )
        )
    ),

    array(
        array(
            "name" => "product_id",
            "type" => "comboboxdb",
            "caption" => "Product",
            "source" => array(
                "table" => "bs_products",
                "name" => "name",
                "value" => "id"
            )
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
