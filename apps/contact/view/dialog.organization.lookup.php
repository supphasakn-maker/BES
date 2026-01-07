<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			?>
			<table id="tblOrganization" class="table table-striped table-bordered table-hover table-middle" width="100%">
				<thead>
					<tr>
						<th class="text-center">
							
						</th>
						<th class="text-center">Code</th>
						<th class="text-center">Organization</th>
						<th class="text-center">Contact</th>
						<th class="text-center">Tax-ID</th>
						<th class="text-center">Type</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<?php
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_organization_lookup","Organization Lookup");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Select",'fn.app.contact.organization.select('.$_POST['callback'].')'),
	));
	$modal->EchoInterface();
	$dbc->Close();
?>
