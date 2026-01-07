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
$usd = $dbc->GetRecord("bs_usd_profit_daily", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_usd", "Edit USD #" . $_POST['id']);
$modal->initiForm("form_editusd");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.profit_loss.daily.edit_fx()")
));
$modal->SetVariable(array(
    array("id", $usd['id'])
));

$readonly = true;
if (strtotime($usd['created']) > strtotime(date("Y-m-d"))) {
    $readonly = true;
}
if ($os->allow("purchase", "edit_special")) $readonly = "false";

$blueprint = array(
    array(
        array(
            "type" => "comboboxdatabank",
            "source" => "db_bank",
            "name" => "bank",
            "caption" => "Bank",
            "value" => $usd['bank']
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
            "value" => $usd['type']
        )
    ),
    array(
        array(
            "name" => "amount",
            "caption" => "Amount",
            "flex" => 4,
            "readonly" => $readonly,
            "placeholder" => "Amount To Purchase",
            "value" => $usd['amount']

        ),
        array(
            "name" => "rate_exchange",
            "caption" => "Exchange",
            "placeholder" => "Exchange Rate",
            "flex" => 4,
            "value" => $usd['rate_exchange']
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "placeholder" => "Purchase Date",
            "value" => $usd['date']
        )
    ),
    array(
        array(
            "name" => "method",
            "type" => "combobox",
            "caption" => "Method",
            "value" => $usd['method'],
            "flex" => 2,
            "source" => array(
                "Today",
                "Forward",
                "TOM",
                "SPOT",
                "1D",
                "1W",
                "1M",
                "2M",
                "3M",
            )
        ),
        array(
            "name" => "ref",
            "caption" => "Deal ID",
            "flex" => 6,
            "placeholder" => "Reference",
            "value" => $usd['ref']
        )
    ),
    array(
        array(
            "type" => "textarea",
            "name" => "comment",
            "caption" => "Comment",
            "placeholder" => "Comment",
            "value" => $usd['comment']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
