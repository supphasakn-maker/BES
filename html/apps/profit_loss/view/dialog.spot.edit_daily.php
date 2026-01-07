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
$spot = $dbc->GetRecord("bs_spot_profit_daily", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_spot", "Edit Spot");
$modal->initiForm("form_editspot");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.profit_loss.daily.edit_spot()")
));
$modal->SetVariable(array(
    array("id", $spot['id'])
));

$readonly = true;
if (strtotime($spot['created']) > strtotime(date("Y-m-d"))) {
    $readonly = true;
}

if ($os->allow("purchase", "edit_special")) $readonly = "false";

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
            ),
            "value" => $spot['supplier_id']
        )
    ),
    array(
        array(
            "name" => "type",
            "type" => "combobox",
            "caption" => "Type",
            "source" => array(
                array("Long", "Long"),
                array("Short", "Short"),
            ),
            "value" => $spot['type']
        )
    ),
    array(
        array(
            "name" => "amount",
            "caption" => "Amount",
            "placeholder" => "Amount To Purchase",
            "value" => $spot['amount'],
            "readonly" => $readonly,
            "flex" => 6
        ),
        array(
            "name" => "currency",
            "caption" => "Currency",
            "type" => "comboboxdb",
            "source" => array(
                "table" => "bs_currencies",
                "value" => "code",
                "name" => "code"
            ),
            "value" => $spot['currency'],
            "flex" => 2
        )
    ),
    array(
        array(
            "name" => "THBValue",
            "caption" => "THBValue",
            "placeholder" => "Total Purchase in THB",
            "value" => $spot['THBValue']
        )
    ),
    array(
        array(
            "name" => "rate_spot",
            "caption" => "Spot",
            "placeholder" => "Spot Name",
            "flex" => 4,
            "readonly" => $readonly,
            "value" => $spot['rate_spot']
        ),
        array(
            "name" => "rate_pmdc",
            "caption" => "Pm/Dc",
            "placeholder" => "premium/discount",
            "flex" => 4,
            "value" => $spot['rate_pmdc']
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "placeholder" => "Purchase Date",
            "value" => $spot['date']
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "value_date",
            "caption" => "Value Date",
            "placeholder" => "Value Date",
            "value" => $spot['value_date']
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
            ),
            "value" => $spot['method']
        ),
        array(
            "name" => "ref",
            "caption" => "Reference",
            "flex" => 6,
            "placeholder" => "Reference",
            "value" => $spot['ref']
        )
    ),
    array(
        array(
            "name" => "noted",
            "type" => "combobox",
            "caption" => "NOTE",
            "source" => array(
                array("Normal", "Normal"),
                array("Close-Adjust", "Close-Adjust"),
                array("Open-Adjust", "Open-Adjust")
            ),
            "value" => $spot['noted']
        )
    ),
    array(
        array(
            "type" => "textarea",
            "name" => "comment",
            "caption" => "Comment",
            "value" => $spot['comment']
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
            ),
            "value" => $spot['adj_supplier']
        )
    ),
    array(
        array(
            "name" => "product_id",
            "caption" => "Product",
            "type" => "comboboxdb",
            "source" => array(
                "table" => "bs_products",
                "value" => "id",
                "name" => "name"
            ),
            "value" => $spot['product_id']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
