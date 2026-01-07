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
$incoming = $dbc->GetRecord("bs_smg_stx_receiving", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_incoming", "Edit Incoming");
$modal->initiForm("form_editincoming");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.sigmargin_stx.incoming.edit()")
));
$modal->SetVariable(array(
    array("id", $incoming['id'])
));

$blueprint = array(

    array(
        array(
            "type" => "number",
            "name" => "amount",
            "caption" => "KGS",
            "value" => $incoming['amount']
        )
    ),
    array(
        array(
            "type" => "number",
            "name" => "rate_pmdc",
            "caption" => "Pm / Dc",
            "value" => $incoming['rate_pmdc']
        )
    ),
    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "value" => date("Y-m-d"),
            "flex" => 4,
            "value" => $incoming['date']
        )
    ),
    array(
        array(
            "type" => "number",
            "name" => "transfer",
            "caption" => "ค่าสินค้า",
            "value" => $incoming['transfer']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
