<?php
	global $os;
	$sys = json_decode(file_get_contents("../../config/system.json"),true);
	$iform = new iform($this->dbc,$this->auth);
	$iform->setFrom("form_setting");
	
	$bAccount = $os->load_variable("bAccount","string");
	$multicurrency = $os->load_variable("multicurrency","string");
	
	$blueprint = array(
		array(
			array(
				"caption"=>"",
				"type" => "switchbox",
				"name" => "bAccount",
				"value" => $bAccount == "yes" ? "yes":"no",
				"text" => "Account System",
			)
		),array(
			array(
				"caption"=>"",
				"type" => "switchbox",
				"name" => "multicurrency",
				"value" => $multicurrency == "yes" ? "yes":"no",
				"text" => "Multi-Currency",
			)
		),array(
			array(
				"caption"=>"",
				"type" => "switchbox",
				"name" => "chatsystem",
				"value" => $os->load_variable("chatsystem","string") == "yes" ? "yes":"no",
				"text" => "Chatsystem",
			)
		),array(
			array(
				"caption"=>"",
				"type" => "switchbox",
				"name" => "quicklaunch",
				"value" => $os->load_variable("quicklaunch","string") == "yes" ? "yes":"no",
				"text" => "Quicklaunch",
			)
		),array(
			array(
				"flex" => 4,
				"caption"=>"Login Timeout",
				"name" => "sessiontimeout",
				"value" => $os->load_variable("sessiontimeout","string")
			),array(
				"flex" => 4,
				"caption"=>"Logon Timeout",
				"name" => "cookietimeout",
				"value" => $os->load_variable("cookietimeout","string")
			)
		),"hr"
		,array(
			array(
				"type" => "button",
				"name" => "Save",
				"onclick" => "fn.app.zystem.core.initial.save_core()",
				"class" => "btn btn-danger float-right"
			)
		)
	);
	$iform->SetBlueprint($blueprint);
?>
<div class="row">
	<div class="col-lg-12 col-xl-12 order-lg-12 order-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>Core System<span class="fw-300"><i>Specialist Only</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
				<?php
					$iform->EchoInterface();
				?>
				</div>
			</div>
		</div>
		<div id="panel-" class="panel">
			<div class="panel-hdr">
				<h2>Company Information<span class="fw-300"><i>for autrhorized only</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
				<?php
				if(is_null($this->auth['account'])){
					echo "Your group is not belong to any organization?";
					$company = json_decode($os->load_variable('aCompany_info',"json"),true);
					?>
					<table class="table table-bordered table-striped table-editable" style="clear: both">
					<thead>
						<tr>
							<th class="text-center" width="35%">Attribute</th>
							<th class="text-center" width="65%">Value</th>
						</tr>
					</head>
					<tbody>
						<tr><td>ชื่อองค์กรหรือบริษัท</td><td><?php echo aGet($company,'org_name');?></td></tr>
						<tr><td>หมายเลขผู้เสียภาษี</td><td><?php echo aGet($company,'org_tax');?></td></tr>
						<tr><td>เบอร์โทรศัพท์</td><td><?php echo aGet($company,'phone');?></td></tr>
						<tr><td>เบอร์แฟกซ์</td><td><?php echo aGet($company,'email');?></td></tr>
						<tr><td>อีเมลล์</td><td><?php echo aGet($company,'website');?></td></tr>
						<tr><td>เว็บไซต์</td><td><?php echo aGet($company,'branch');?></td></tr>
						<tr><td>ที่อยู่</td><td><?php echo aGet($company,'address');?></td></tr>
					</tbody>
				<?php
				}else{
				?>
					<table class="table table-bordered table-striped table-editable" style="clear: both">
					<thead>
						<tr>
							<th class="text-center" width="35%">Attribute</th>
							<th class="text-center" width="65%">Value</th>
						</tr>
					</head>
					<tbody>
						<tr><td>ชื่อองค์กรหรือบริษัท</td><td><?php echo aGet($company,'org_name');?></td></tr>
						<tr><td>หมายเลขผู้เสียภาษี</td><td><?php echo aGet($company,'org_tax');?></td></tr>
						<tr><td>เบอร์โทรศัพท์</td><td><?php echo aGet($company,'phone');?></td></tr>
						<tr><td>เบอร์แฟกซ์</td><td><?php echo aGet($company,'email');?></td></tr>
						<tr><td>อีเมลล์</td><td><?php echo aGet($company,'website');?></td></tr>
						<tr><td>เว็บไซต์</td><td><?php echo aGet($company,'branch');?></td></tr>
						<tr><td>ที่อยู่</td><td><?php echo aGet($company,'address');?></td></tr>
					</tbody>
				<?php
				}
				?>	
				</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php

	
	$company = json_decode($os->load_variable('aCompany_info',"json"),true);
	
	function create_editable_text($caption,$field,$variable,$value,$title){
		echo '<tr>';
			echo '<td>'.$caption.'</td>';
			echo '<td>';
				echo '<a class="editable" data-name="'.$field.'" data-type="text" data-pk="'.$variable.'" data-title="'.$title.'">'.$value.'</a>';
			echo '</td>';
		echo '</tr>';
	}
	
	function aGet($array,$variable){
		if(isset($array[$variable])){
			return base64_decode($array[$variable]);
		}else{
			return "";
		}
	}
	
?>
<div class="widget-body-toolbar">
	<div class="row">
		<div class="col-sm-6">
			<button id="enable" class="btn btn btn-outline-dark" onclick="">
				แก้ไข
			</button>
		</div>
		<div class="col-sm-6 text-right">			
						
		</div>					
	</div>
</div>