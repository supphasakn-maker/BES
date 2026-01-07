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
$usd = $dbc->GetRecord("bs_profit_daily", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_daily", "Edit NOTED #" . $_POST['id']);
$modal->initiForm("form_editdialy");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-outline-dark", "Save Change", "fn.app.profit_loss.daily.edit()")
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
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "placeholder" => "Purchase Date",
            "value" => $usd['date']
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
