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
			if($_POST['type']=="contact"){
				$contact = $dbc->GetRecord("os_contacts","*","id=".$_POST['id']);
				$ref_id = $contact['id'];
			}else{
				$ref_id = $_POST['id'];
			}
			
			?>
				<table id="tblAddress" class="table table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center">
								<span type="checkall" control="chk_address" class="fa fa-lg fa-square-o"></span>
							</th>
							<th class="text-center">Address</th>
							<th class="text-center">Country</th>
							<th class="text-center">City</th>
							<th class="text-center">District</th>
							<th class="text-center">Subdistrict</th>
							<th class="text-center">Postal</th>
							<th class="text-center">Remark</th>
							<th class="text-center">Action</th>
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
	$modal->setModel("dialog_address","Address");
	$modal->setExtraClass("modal-full");
	$modal->setButton(array(
		array("action","btn-primary","Add",'fn.app.contact.address.dialog_add(\''.$_POST['type'].'\','.$_POST['id'].')'),
		array("close","btn-secondary","Dismiss")
	));
	
	$modal->EchoInterface();
	
	$dbc->Close();
?>