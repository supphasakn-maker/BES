<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',1);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);
	
	$user = $dbc->GetRecord("users","*","id=".$_SESSION['auth']['user_id']);
	$setting = json_decode($user['setting'],true);
	
	
	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_sendmail","Send E-mail");
	$modal->initiForm("form_sendmail");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.profile.mail.sendmail()")
	));
	$modal->SetVariable(array(
		array("txtID",$user['id'])
	));
	
	$datetime = $os->LoadSetting('datetime');
	$mail = $os->LoadSetting('mail');

	$blueprint = array(
		array(
			array(
				"caption" => "Topic",
				"name" => "topic",
				"placeholder" => "Your E-mail Topic"
			)
		),
		array(
			array(
				"type" => "textarea",
				"rows" => "15",
				"caption" => "Body",
				"name" => "body",
				"placeholder" => ""
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>