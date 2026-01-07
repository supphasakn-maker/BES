<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$user_id = intval($_SESSION['auth']['user_id']);
$user_data = $dbc->GetRecord("os_users", "*", "id=" . $user_id);

$can_delete = false;

if ($user_data && isset($user_data['gid'])) {
	$user_gid = intval($user_data['gid']);

	if (in_array($user_gid, array(1))) {
		$can_delete = true;
	}
}

if (!$can_delete) {
	echo "<div class='alert alert-danger'>";
	echo "<i class='fas fa-exclamation-triangle'></i> ";
	echo "<strong>ไม่มีสิทธิ์:</strong> คุณไม่มีสิทธิ์ในการลบ Order กรุณาติดต่อผู้ดูแลระบบ";
	echo "</div>";
	$dbc->Close();
	exit;
}

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
		$items = isset($this->param['items']) ? $this->param['items'] : array();
		$removable = true;

		if (count($items) == 0) {
			$removable = false;
		}

		if ($removable) {
			echo '<ul>';
			foreach ($items as $item) {
				$order = $dbc->GetRecord("bs_orders", "*", "id=" . intval($item));

				if (!$order) {
					echo "<li class='text-danger'>ไม่พบ Order ID: " . htmlspecialchars($item) . "</li>";
					continue;
				}

				echo "<li>" . htmlspecialchars($order['id']) . ' : ' . htmlspecialchars($order['code']);

				if (!is_null($order['parent'])) {
					$deletable = true;
					$sql = "SELECT * FROM bs_orders WHERE parent = " . intval($order['parent']) . " AND id != " . intval($item);
					$rst = $dbc->Query($sql);
					$extra_string = "<br>";

					while ($line = $dbc->Fetch($rst)) {
						if ($dbc->HasRecord("bs_orders", "parent=" . intval($line['id']))) {
							$deletable = false;
						}
						$extra_string .= " <span class='badge badge-warning'>เอกสาร " . htmlspecialchars($line['code']) . " จะถูกลบด้วย</span>";
					}

					if ($deletable) {
						echo $extra_string;
					} else {
						echo " <span class='badge badge-danger'>ไม่สามารถลบได้เนื่องจากมีรายการย่อย</span>";
					}
				}

				echo "</li>";
			}
			echo '</ul>';
		} else {
			echo '<div class="alert alert-warning">กรุณาเลือกรายการที่ต้องการลบ</div>';
		}
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_remove_order", "Remove Order");

$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Remove", "fn.app.sales.order.remove()")
));

$modal->EchoInterface();

$dbc->Close();
