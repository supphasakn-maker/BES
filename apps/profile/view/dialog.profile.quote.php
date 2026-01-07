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
	
	$user = $dbc->GetRecord("users","*","id=".$_SESSION['auth']['user_id']);
	$contact = $dbc->GetRecord("contacts","*","id=".$user['contact']);
	$address = $dbc->GetRecord("address","*","contact=".$contact['id']);
	$setting = json_decode($user['setting'],true);
	
	if(isset($setting['quote'])){
		$quote = $setting['quote'];
	}else{
		$quote = array(
			"title" => base64_encode(""),
			"detail" => base64_encode("") 
		);
	}
	
	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST);
	$modal->setModel("dialog_edit_quote","Edit Quote");
	$modal->initiForm("form_editquote");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.profile.setquote()")
	));
	$modal->SetVariable(array(
		array("txtID",$user['id'])
	));
	
	
	
	$blueprint = array(
		array(
			array(
				"name" => "txtTitle",
				"caption" => "About You",
				"placeholder" => "Quote yourself",
				"value" =>base64_decode($quote['title'])
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "txtDetail",
				"caption" => "More Detail",
				"placeholder" => "Explain More",
				"value" => base64_decode($quote['detail'])
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>