<?php
	global $os;
	$sys = json_decode(file_get_contents("../../config/system.json"),true);
	$iform = new iform($this->dbc,$this->auth);
	$iform->setFrom("form_setting");
	
	$items = array(
		array('default_language','cbbDefaultLanguage'),
		array('default_theme','cbbDefaultThemes'),
		array('default_date_format','txtDefault_DateFormat'),
		array('default_time_format','txtDefault_TimeFormat'),
		array('default_datetime_format','txtDefault_DateTimeFormat')
	);
	
	$blueprint = array(
		array(
			array(
				"caption" => "Timezone",
				"type" => "combobox",
				"name" => "default_timezone",
				"source" => DateTimeZone::listIdentifiers(),
				"help" => "Use PHP format for all setting!",
				"value" => $os->load_variable("default_timezone","string")
			)
		),array(
			array(
				"caption" => "Format",
				"flex" => 5,
				"name" => "default_datetime_format",
				"placeholder" => "Date",
				"value" => $os->load_variable("default_datetime_format","string")
			),
			array(
				"flex" => 5,
				"name" => "default_time_format",
				"placeholder" => "Time",
				"value" => $os->load_variable("default_time_format","string")
			)
		),array(
			array(
				"caption" => "Datetime Format",
				"name" => "default_date_format",
				"placeholder" => "Time",
				"value" => $os->load_variable("default_date_format","string")
			)
		),"hr",
		array(
			array(
				"type" => "combobox",
				"name" => "default_language",
				"caption" => "Language",
				"source" => $sys['language'],
				"config" => array(
					"caption" => "name",
					"value" => "code"
				),
				"value" => $os->load_variable("default_language","string")
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "default_theme",
				"caption" => "Theme",
				"source" => $sys['themes'],
				"config" => array(
					"caption" => "name",
					"value" => "code"
				),
				"value" => $os->load_variable("default_theme","string")
			)
		),"hr"
		,array(
			array(
				"type" => "button",
				"name" => "Save",
				"onclick" => "fn.app.setting.system.save_general()",
				"class" => "btn btn-danger float-right"
			)
		)
	);
	$iform->SetBlueprint($blueprint);
	
	
	
	
	
?>
<div class="row">
	<div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>Default Date and Time <span class="fw-300"><i>default</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
				<?php
					$iform->EchoInterface();
				?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>Language <span class="fw-300"><i>default</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<table class="table">
						<thead>
							<tr>
								<th>Variable</th>
								<th>Value</th>
							</tr>
						</thead>
						<tbody>
							<tr><td>default_timezone</td><td><?php echo $os->load_variable("default_timezone","string");?></td></tr>
							<tr><td>default_datetime_format</td><td><?php echo $os->load_variable("default_datetime_format","string");?></td></tr>
							<tr><td>default_time_format</td><td><?php echo $os->load_variable("default_time_format","string");?></td></tr>
							<tr><td>default_date_format</td><td><?php echo $os->load_variable("default_date_format","string");?></td></tr>
							<tr><td>default_language</td><td><?php echo $os->load_variable("default_language","string");?></td></tr>
							<tr><td>default_theme</td><td><?php echo $os->load_variable("default_theme","string");?></td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
