<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_crucible","Add Crucible");
	$modal->initiForm("form_addcrucible");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.production_crucible.crucible.add()")
	));

	$blueprint = array(
        array(
			array(
				"name" => "start",
				"caption" => "เริ่มต้น",
				"flex" => 4
			)
            ),array(
                array(
                    "name" => "end",
                    "caption" => "จำนวน",
                    "flex" => 4
                )
            ),array(
                array(
                    "name" => "date",
                    "type" => "date",
                    "caption" => "วันที่",
                    "flex" => 4,
                    "value" => date("Y-m-d")
                )
                )
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>
