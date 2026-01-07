<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../menu.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$group = $dbc->GetRecord("groups", "*", "id=" . $_REQUEST['id']);
$roles = array(
	array("setting_system", "Allow privileged user to change system setting."),
	array("setting_company", "Allow privileged user to change company infomration."),
	array("setting_update", "Allow privileged user to update system.")

);

?>
<div class="modal fade" id="dialog_edit_group" data-backdrop="static">
	<div class="modal-dialog">
		<form id="form_editgroup" class="form-horizontal" role="form" onsubmit="fn.app.accctrl.group.save_role();return false;">
			<input type="hidden" name="txtID" value="<?php echo $group['id']; ?>">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4>Edit Role</h4>
				</div>
				<div class="modal-body">
					<h3>Group Name : <?php echo $group['name']; ?></h3>
					<table class="table table-bordered table-condensed table-hover table-striped">
						<thead>
							<tr>
								<th class="text-center">Privilege</th>
								<th class="text-center">Role</th>
							</tr>

							<head>
						<tbody>
							<?php
							foreach ($roles as $role) {
								echo '<tr>';
								echo '<td class="text-center">';
								echo '<label class="label-checkbox">';
								echo '<input name="role[' . $role[0] . ']" type="checkbox">';
								echo '<span class="custom-checkbox"></span>';
								echo '</label>';
								echo '</td>';
								echo '<td>' . $role[1] . '</td>';
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button id="btnSelectAll" type="button" class="btn btn-outline-dark pull-left">Select All</button>
					<button type="button" class="btn btn-outline-dark" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->