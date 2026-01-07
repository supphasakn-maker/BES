<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
?>
<div class="modal fade" id="dialog_user_lookup" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Select User</h4>
			</div>
			<div class="modal-body">
				<table id="tblUserLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center">Select</th>
							<th class="text-center">Avatar</th>
							<th class="text-center">Fullname</th>
							<th class="text-center">Email</th>
							<th class="text-center">Mobile</th>
							<th class="text-center">Group</th>
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