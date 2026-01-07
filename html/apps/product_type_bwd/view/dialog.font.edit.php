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
$fonts = $dbc->GetRecord("bs_fonts_bwd", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_font", "Edit Fonts");
$modal->initiForm("form_editfont");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.product_type_bwd.font.edit()")
));
$modal->SetVariable(array(
    array("id", $fonts['id'])
));

$blueprint = array(
    array(
        array(
            "name" => "name",
            "caption" => "Name",
            "placeholder" => "Name",
            "value" => $fonts['name']
        )
    ),
    array(
        array(
            "type" => "combobox",
            "name" => "status",
            "caption" => "Status",
            "source" => array(
                array(1, "enabled"),
                array(2, "disabled")
            ),
            "value" => $fonts['status']
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
