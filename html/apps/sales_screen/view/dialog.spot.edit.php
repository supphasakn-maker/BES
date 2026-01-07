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
	$modal->setModel("dialog_edit_spot","แก้ไขข้อมูล");
	$modal->initiForm("form_spotedit");
	$modal->setExtraClass("modal-xl");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("close","btn-primary","Save Change")
	));

	$blueprint = array(
		array(
			array(
				"name" => "code",
				"caption" => "SPOT",
				"flex" => 4
			),array(
				"name" => "name",
				"caption" => "อัตราแลกเปลี่ยน",
				"flex" => 4
			)
		),array(
			array(
				"name" => "vip1",
				"caption" => "VIP1",
				"flex" => 4,
				"value" => "-20"
			)
		),array(
			array(
				"name" => "vip2",
				"caption" => "VIP2",
				"flex" => 4,
				"value" => "-40"
			)
		),array(
			array(
				"name" => "vip3",
				"caption" => "VIP3",
				"flex" => 4,
				"value" => "-60"
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>
