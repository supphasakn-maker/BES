<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
?>
<div class="modal fade" id="dialog_deposit_lookup" data-backdrop="static">
  	<div class="modal-dialog modal-full">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>เลือกโอนล่วงหน้า</h4>
      		</div>
		    <div class="modal-body">
				<table id="tblDepositLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center">Select</th>
							<th class="text-center">Date</th>
							<th class="text-center">Amount</th>
							<th class="text-center">Remain</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
		    </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btnSelect">Select</button>
			</div>
	  	</div>
		</form>
	</div>
</div>

<?php
	$dbc->Close();
?>