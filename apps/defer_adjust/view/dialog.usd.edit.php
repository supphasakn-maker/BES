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
$usd = $dbc->GetRecord("bs_match_usd", "*", "id=" . $_POST['id']);


$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_usd", "EDIT USD");
$modal->initiForm("form_editusd");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.defer_adjust.usd.edit()")
));
$modal->SetVariable(array(
    array("id", $usd['id'])
));



$blueprint = array(
    array(
        array(
            "name" => "bank",
            "caption" => "BANK",
            "type" => "comboboxdb",
            "source" => array(
                "table" => " bs_banks",
                "name" => "name",
                "value" => "id",
                "where"  => "id in(3,9,7)",
            ),
            "value" => $usd['bank']
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "flex" => 4,
            "value" => $usd['date']
        )
    ),
    array(
        array(
            "name" => "usd",
            "caption" => "USD",
            "placeholder" => "Total USD",
            "value" => $usd['usd']
        )
    ),
    array(
        array(
            "type" => "textarea",
            "name" => "comment",
            "caption" => "Comment",
            "value" => $usd['comment']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
