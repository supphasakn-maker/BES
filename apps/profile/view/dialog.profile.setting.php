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
	$modal->setModel("dialog_edit_setting","Setting");
	$modal->initiForm("form_editsetting");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.profile.setting()")
	));
	$modal->SetVariable(array(
		array("txtID",$user['id'])
	));
	
	$datetime = $os->LoadSetting('datetime');
	$mail = $os->LoadSetting('mail');

	$blueprint = array(
		array(
			"group" => "setting_group",
			"type" => "tablist",
			"items" => array(
				array(
					"group" => "setting_group_a",
					"name" => "General",
					"type" => "tab",
					"items" => array(
						array(
							array(
								"caption" => "Timezone",
								"type" => "combobox",
								"name" => "setting[datetime][timezone]",
								"source" => DateTimeZone::listIdentifiers(),
								"value" => $datetime['timezone'],
								"help" => "Use PHP format for all setting!"
							)
						),array(
							array(
								"caption" => "Full Date/Time",
								"flex" => 5,
								"name" => "setting[datetime][ldate]",
								"placeholder" => "Date",
								"value" => $datetime['ldate']
							),
							array(
								"flex" => 5,
								"name" => "setting[datetime][ltime]",
								"placeholder" => "Time",
								"value" => $datetime['ltime']
							)
						),array(
							array(
								"caption" => "Short Date/Time",
								"flex" => 5,
								"name" => "setting[datetime][sdate]",
								"placeholder" => "Date",
								"value" => $datetime['sdate']
							),
							array(
								"flex" => 5,
								"name" => "setting[datetime][stime]",
								"placeholder" => "Time",
								"value" => $datetime['stime']
							)
						)
					)
				),
				array(
					"group" => "setting_group_b",
					"name" => "Basic",
					"type" => "tab",
					"items" => array(
						array(
							array(
								"type" => "combobox",
								"source" => array(
									array("main","Main Language"),
									array("alt","Alternative")
								),
								"caption" => "Datasource",
								"name" => "setting[config][datasource]",
								"value" => isset($setting['config']['datasource'])?$setting['config']['datasource']:"main"
							)
						),array(
							array(
								"caption" => "Datatable Rows",
								"name" => "setting[config][datatable][row]",
								"value" => isset($setting['config']['datatable']['row'])?$setting['config']['datatable']['row']:"10"
							)
						)
					)
				),
				array(
					"group" => "setting_group_c",
					"name" => "E-mail",
					"type" => "tab",
					"items" => array(
						array(
							array(
								"caption" => "E-mail Address",
								"name" => "setting[mail][email]",
								"placeholder" => "Your E-Mail Address",
								"value" => $mail['email']
							)
						),
						"hr",
						array(
							array(
								"type" => "combobox",
								"flex" => 2,
								"source" => array(
									array("imap","IMAP"),
									array("pop","POP3")
								),
								"caption" => "Incoming Server",
								"name" => "setting[mail][in][type]",
								"value" => $mail['in']['type']
							),
							array(
								"flex" => 8,
								"name" => "setting[mail][in][server]",
								"placeholder" => "Your Server Address",
								"value" => $mail['in']['server']
							)
						),
						array(
							array(
								"caption" => "Username",
								"flex" => 5,
								"name" => "setting[mail][in][username]",
								"placeholder" => "Your Username",
								"value" => $mail['in']['username']
							),
							array(
								"type" => "password",
								"flex" => 5,
								"name" => "setting[mail][in][password]",
								"placeholder" => "Your Password",
								"value" => $mail['in']['password']
							)
						),
						array(
							array(
								"type" => "combobox",
								"source" => array(
									array("none","None"),
									array("ssl","SSL"),
									array("tls","TLS")
								),
								
								"flex" => 4,
								"caption" => "Security",
								"name" => "setting[mail][in][security]",
								"value" => $mail['in']['security']
							),array(
								"flex" => 4,
								"caption" => "Port Number",
								"name" => "setting[mail][in][port]",
								"value" => $mail['in']['port']
							)
						),
						"hr",
						array(
							array(
								"type" => "combobox",
								"flex" => 2,
								"source" => array(
									array("smtp","SMTP")
								),
								"caption" => "Outgoing Server",
								"name" => "setting[mail][out][type]",
								"value" => $mail['out']['type']
							),
							array(
								"flex" => 8,
								"name" => "setting[mail][out][server]",
								"placeholder" => "Your Server Address",
								"value" => $mail['out']['server']
							)
						),
						array(
							array(
								"caption" => "Username",
								"flex" => 5,
								"name" => "setting[mail][out][username]",
								"placeholder" => "Your Username",
								"value" => $mail['out']['username']
							),
							array(
								"type" => "password",
								"flex" => 5,
								"name" => "setting[mail][out][password]",
								"placeholder" => "Your Password",
								"value" => $mail['out']['password']
							)
						),
						array(
							array(
								"type" => "combobox",
								"source" => array(
									array("none","None"),
									array("ssl","SSL"),
									array("tls","TLS")
								),
								"flex" => 4,
								"caption" => "Security",
								"name" => "setting[mail][out][security]",
								"value" => $mail['out']['security']
							),array(
								"flex" => 4,
								"caption" => "Port Number",
								"name" => "setting[mail][out][port]",
								"value" => $mail['out']['port']
							)
						),
						
					)
				)
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>