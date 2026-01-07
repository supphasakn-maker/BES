<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
?>
<div class="modal fade" id="dialog_contact_lookup" data-backdrop="static">
  	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Organization Lookup</h4>
      		</div>
		    <div class="modal-body">
				<table id="tblOrganization" class="table table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center"></th>
							<th class="text-center">Code</th>
							<th class="text-center">Organization</th>
							<th class="text-center">Contact</th>
							<th class="text-center">Industry</th>
							<th class="text-center">Type</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
		    </div>
			<div class="modal-footer">
				<button onclick="fn.app.contact.contact.dialog_add()" type="button" class="btn btn-success pull-left">New Company</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button onclick="fn.app.contact.contact.select()" type="submit" class="btn btn-primary">Select</button>
			</div>
	  	</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->