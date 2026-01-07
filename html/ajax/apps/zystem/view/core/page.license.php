<?php
	global $os;
	$sys = json_decode(file_get_contents("../../config/system.json"),true);
	
	if(file_exists("../../config/server.key")){
		$has_key = true;
		$code = file_get_contents("../../config/server.key");
	}else{
		$has_key = false;
	}
	
	$iform = new iform($this->dbc,$this->auth);
	$iform->setFrom("form_setting");
	
	$blueprint = array(
		array(
			array(
				"type" => "combobox",
				"name" => "machine_type",
				"caption" => "Type",
				"placeholder" => "Type of Machine",
				"source" => array(
					array("dedicated","Dedicated Server"),
					array("embeded","Embedded Server"),
					array("workstation","Workstation"),
					array("wmware","VMware vSphere"),
					array("wmware workstation","VMware Workstation"),
					array("wmware esxi","vSphere Hypervisor"),
					array("qemu","QEMU"),
					array("hyperv","Hyper-V"),
					array("kvm","KVM"),
					array("citrix","Citrix Hypervisor"),
					array("azure","Azure Virtual Machines"),
					array("xen","Xen Project"),
					array("icm","IBM Cloud for VMware Solutions"),
					array("virtualbox","VirtualBox"),
					array("aws","Amazon Web Service"),
					array("digital ocean","DigitalOcean")
				)
			)
		),array(
			array(
				"name" => "machine_id",
				"caption" => "Machine ID",
				"placeholder" => "Unique ID of Server"
			)
		),array(
			array(
				"name" => "brand",
				"caption" => "Brand",
				"placeholder" => "Server's Brand",
				"flex" => 4
			),
			array(
				"name" => "model",
				"caption" => "Model",
				"placeholder" => "Server's Model",
				"flex" => 4
			)
		),array(
			array(
				"caption" => "",
				"type" => "button",
				"name" => "Initial License Code",
				"onclick" => "fn.app.zystem.core.license.create()",
				"class" => "btn btn-danger"
			)
		)
	);
	$iform->SetBlueprint($blueprint);
?>
<div class="row">
<?php
if($has_key){
?>
	<div class="col-lg-12 col-xl-12 order-lg-12 order-xl-12">
		<div id="panel-e" class="panel">
			<div class="panel-hdr">
				<h2>Current Key<span class="fw-300"><i>Please do not change!</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
				<pre>
				<?php
				$code = json_decode(base64_decode(base64_decode($code)),true);
				var_dump($code);
				?>
				</pre>
				</div>
			</div>
		</div>
	</div>
<?php
}
?>
	<div class="col-lg-12 col-xl-12 order-lg-12 order-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>License Management<span class="fw-300"><i>Specialist Only</i></span></h2>
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
</div>
