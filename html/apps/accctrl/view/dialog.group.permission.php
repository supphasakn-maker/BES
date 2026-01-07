<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";
//include_once "../../menu.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);


class myModel extends imodal
{
	private $ctrlitems = array(
		array("view", "View"),
		array("add", "Add"),
		array("edit", "Edit"),
		array("remove", "Remove"),
		array("special", "Special"),
		array("approve", "Approve")
	);

	function body()
	{
		$dbc = $this->dbc;
		$aApplication = json_decode(file_get_contents("../../menu.json"), true);
		$group = $dbc->GetRecord("os_groups", "*", "id=" . $this->param['id']);

		$apps = array();
		$appnames = array();
		foreach ($aApplication as $menu) {
			if (!in_array($menu['appname'], $apps)) {
				array_push($apps, $menu['appname']);
				array_push($appnames, $menu['name']);
			}
			if (isset($menu['submenu'])) {
				foreach ($menu['submenu'] as $submenu) {
					if (!in_array($submenu['appname'], $apps)) {
						array_push($apps, $submenu['appname']);
						array_push($appnames, $submenu['name']);
					}
				}
			}
		}

?>
		<form name="form_edit_permission">
			<input type="hidden" name="group_id" value="<?php echo $_POST['id']; ?>">
			<table class="table table-bordered table-condensed table-hover table-striped">
				<thead>
					<tr>
						<th class="text-center">Application</th>
						<th class="text-center" width="20">View</th>
						<th class="text-center" width="20">Add</th>
						<th class="text-center" width="20">Edit</th>
						<th class="text-center" width="20">Remove</th>
						<th class="text-center" width="20">ทำรายการย้อนหลัง</th>
						<th class="text-center" width="20">Approve</th>
					</tr>

					<head>
				<tbody>
					<?php
					for ($i = 0; $i < count($apps); $i++) {
						$app = $apps[$i];
						$appname = $appnames[$i];
						echo '<tr>';
						echo '<td class="checkrow pointer">' . $appname . '</td>';
						foreach ($this->ctrlitems as $ctrl) {
							$name = $app . "_" . $ctrl[0];
							$granted = $dbc->hasRecord("os_permissions", "name='$app' AND action='" . $ctrl[0] . "' AND gid=" . $group['id']);
							echo '<td>';
							echo '<div class="custom-control custom-checkbox">';
							echo '<input type="checkbox" class="custom-control-input" id="' . $name . '" name="permission[' . $app . '][' . $ctrl[0] . ']"' . ($granted ? " checked" : "") . '>';
							echo '<label class="custom-control-label" for="' . $name . '"></label>';
							echo '</div>';
							echo '</td>';
						}
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</form>
<?php


	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_edit_group", "Edit Group Permission");

$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("action", "btn-danger", "Deselect All", "$('.custom-control input:checkbox').prop('checked', false )"),
	array("action", "btn-info", "Select All", "$('.custom-control input:checkbox').prop('checked', true )"),
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-warning", "Save", "fn.app.accctrl.group.save_permission()")
));

$modal->EchoInterface();

$dbc->Close();
?>