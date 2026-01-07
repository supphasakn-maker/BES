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
	$silver = $dbc->GetRecord("bs_mapping_silvers","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_remark_silver","remark silver");
	$modal->initiForm("form_remarksilver");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.match.silver.remark()")
	));
	$modal->SetVariable(array(
		array("id",$silver['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "textarea",
				"name" => "remark",
				"caption" => "remark",
				"placeholder" => "silver Name",
				"value" => $silver['remark']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
