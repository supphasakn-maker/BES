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
$modal->setModel("dialog_add_daily", "Add");
$modal->initiForm("form_adddaily");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Change", "fn.app.profit_loss.daily.add()")
));

$date_filter = isset($_POST['date_filter']) ? $_POST['date_filter'] : date("Y-m-d");


$blueprint = array(

    array(
        array(
            "type" => "date",
            "name" => "date",
            "caption" => "Date",
            "placeholder" => "Date",
            "value" =>  $date_filter
        )
    ),
    array(
        array(
            "type" => "textarea",
            "name" => "comment",
            "caption" => "Comment",
            "placeholder" => "Comment"
        )
    )
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
