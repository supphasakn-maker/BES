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
$produce = $dbc->GetRecord("bs_productions_switch", "*", "id=" . $_POST['id']);


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_switch", "SWAP");
$modal->initiForm("form_addswitch");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.production_switch.switch.edit()")
));

$modal->SetVariable(array(
    array("id", $produce['id'])
));


$blueprint = array(
    array(
        array(
            "name" => "amount",
            "caption" => "จำนวนที่คืน",
            "placeholder" => "amount"
        ),
    ),
    array(
        array(
            "name" => "amount_balance",
            "caption" => "จำนวนที่ยืม",
            "placeholder" => "amount",
            "value" => $produce['weight_out_packing'],
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
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
