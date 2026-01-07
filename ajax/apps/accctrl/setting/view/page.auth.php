<?php
	global $os;
	$sys = json_decode(file_get_contents("../../config/system.json"),true);
	$iform = new iform($this->dbc,$this->auth);
	$iform->setFrom("form_auth");
	
	$item = json_decode($os->load_variable("aLDAPSetting","json"),true);
	

	
	$blueprint = array(
		array(
			array(
				"caption" => "LDAP Type",
				"type" => "combobox",
				"name" => "type",
				"source" => array(
					"openldap" => "OpenLDAP",
					"apache" => "Apache Directory Server",
					"fedora" => "Fedora Directory Server",
					"microsoft" => "Microsoft Active Directory Server",
					"novell" => "Novell eDirectory",
					"oracle" => "Oracle Internet Directory"
				),
				"value" => isset($item['type'])?$item['type']:""
			)
		),array(
			array(
				"caption" => "Base Provider",
				"flex" => 5,
				"name" => "url",
				"placeholder" => "ldap://localhost:10389",
				"value" => isset($item['url'])?$item['url']:""
			)
		),array(
			array(
				"caption" => "Base DN",
				"flex" => 5,
				"name" => "basedn",
				"placeholder" => "dc=example,dc=com",
				"value" => isset($item['basedn'])?$item['basedn']:""
			)
		),array(
			array(
				"caption" => "Principal",
				"flex" => 5,
				"name" => "principal",
				"placeholder" => "uid=admin,ou=system",
				"value" => isset($item['principal'])?$item['principal']:""
			)
		),array(
			array(
				"caption" => "Credentials",
				"flex" => 5,
				"name" => "password",
				"value" => isset($item['password'])?$item['password']:""
			)
		)
		,array(
			array(
				"type" => "button",
				"name" => "Save",
				"onclick" => "fn.app.setting.system.save_setting()",
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
				<h2>User Authenication</h2>
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
				<h2>Language <span class="fw-300"><i>output</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<pre>
<?php
				/*the connecting part*/
				$ldap_conn = ldap_connect("192.168.1.50", 389);
				$bind = ldap_bind($ldap_conn);
				/*the connecting part*/
/*
				$samaccountname = john.doe;

				$filter="(samaccountname=$samaccountname)";
				$dn="OU=PEOPLE, DC=example, DC=com"; //even if it seems obvious I note here that the dn is just an example, you'll have to provide an OU and DC of your own

				$res = ldap_search($ldap_conn, $dn, $filter);
				$first = ldap_first_entry($ldap_conn, $res);
				$data = ldap_get_dn($ldap_conn, $first);

				echo "The desired DN is: ".$data;
				*/

?>					
					
					</pre>
				
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
