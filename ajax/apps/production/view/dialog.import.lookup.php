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
			<table id="tblImportLookup" class="table table-striped table-bordered table-hover table-middle" width="100%">
				<thead>
					<tr>
						<th class="text-center">
							<span type="checkall" control="chk_address" class="fa fa-lg fa-square-o"></span>
						</th>
						<th class="text-center">Delivery Date</th>
						<th class="text-center">Supplier</th>
						<th class="text-center">Amount</th>
						<th class="text-center">Delivery By</th>
						<th class="text-center">Type</th>
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
	$modal->setModel("dialog_import_lookup","Select Import");
	$modal->setExtraClass("modal-full");
	$modal->setButton(array(
		array("action","btn-primary","Append",'fn.app.production.import.select()'),
		array("close","btn-secondary","Dismiss")
	));
	
	$modal->EchoInterface();
	
	$dbc->Close();
?>