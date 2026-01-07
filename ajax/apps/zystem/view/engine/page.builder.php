<?php
	global $os;

	//$user = $this->dbc->GetRecord("os_users","*","id=".$_SESSION['auth']['user_id']);
	//$setting = json_decode($user['setting'],true);
	
	$modal = new iform($this->dbc,$this->auth);
	$modal->setFrom("form_appbuilder");


	$blueprint = array(
		array(
			"group" => "setting_group",
			"type" => "tablist",
			"items" => array(
				array(
					"group" => "group_a",
					"name" => "General",
					"type" => "tab",
					"items" => array(
						array(
							array(
								"caption" => "Name",
								"flex" => 4,
								"name" => "appname",
								"placeholder" => "Application Name"
							),array(
								"caption" => "Icon",
								"flex-label" => 1,
								"flex" => 5,
								"name" => "icon",
								"placeholder" => "Icon Code"
							)
						),
						array(
							array(
								"caption" => "Caption",
								"name" => "name",
								"placeholder" => "Caption"
							)
						),
						array(
							array(
								"type" => "combobox",
								"caption" => "Type",
								"source" => array(
									"standard" => "Standard", 
									"blank" => "Blank"
								),
								"name" => "Show Name",
								"placeholder" => "Caption"
							)
						)
					)
				),
				array(
					"group" => "group_b",
					"name" => "Application",
					"type" => "tab",
					"items" => array(
						array(
							array(
								"type" => "button",
								"class" => "btn btn-primary",
								"name" => "Add SubApplication",
								"onclick" => "fn.app.zystem.engine.builder.append_subapp()"
							)
						),array(
							"group" => "sub_app_zone",
							"items" => array()
						)
					)
				)
			)
		),'hr',
		array(
			array(
				"type" => "button",
				"class" => "btn btn-primary float-right",
				"name" => "Build",
				"onclick" => "fn.app.zystem.engine.builder.build()"
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();

?>