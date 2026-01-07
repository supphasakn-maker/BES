<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
?>
<div class="modal fade" id="dialog_address_lookup" data-backdrop="static">
  	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Address Lookup</h4>
      		</div>
		    <div class="modal-body">
				<table id="tblAddressLookup" class="table table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th></th>
							<th class="text-center">Address</th>
							<th class="text-center">Country</th>
							<th class="text-center">City</th>
							<th class="text-center">District</th>
							<th class="text-center">Subdistrict</th>
							<th class="text-center">Postal</th>
                            <th class="text-center">Type</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
		    </div>
			<div class="modal-footer">
				<button onclick="fn.app.contact.address.dialog_add()" type="button" class="btn btn-success pull-left">New Address</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary btnSelect">Select</button>
			</div>
	  	</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
	$dbc->Close();
?>