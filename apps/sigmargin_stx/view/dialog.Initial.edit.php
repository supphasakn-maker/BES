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
$Initial = $dbc->GetRecord("bs_smg_stx_initial", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_Initial", "Edit Initial");
$modal->initiForm("form_editInitial");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.sigmargin_stx.Initial.edit()")
));
$modal->SetVariable(array(
    array("id", $Initial['id'])
));

$blueprint = array(
    array(
        array(
            "type" => "date",
            "name" => "date_start",
            "caption" => "Date Form",
            "value" => $Initial['date_start'],
            "flex" => 4,
        ),
        array(
            "type" => "date",
            "name" => "date_end",
            "caption" => "Date To",
            "value" => $Initial['date_end'],
            "flex" => 4
        )
    ),
    array(
        array(
            "type" => "number",
            "name" => "margin",
            "caption" => "Initial Margin",
            "placeholder" => "Initial Margin",
            "value" => $Initial['margin']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
