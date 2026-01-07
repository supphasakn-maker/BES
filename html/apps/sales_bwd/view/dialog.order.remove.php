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
				$order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $item);
				echo "<li>" . $order['id'] . ' : ' . $order['code'];
				if (!is_null($order['parent'])) {
					$deletable = true;
					$sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $order['parent'] . " AND id != " . $item;
					$rst = $dbc->Query($sql);
					$extra_string = "<br>";
					while ($line = $dbc->Fetch($rst)) {
						if ($dbc->HasRecord("bs_orders_bwd", "parent=" . $line['id'])) {
							$deletable = false;
						}
						$extra_string .= " <span class='badge badge-warning'>เอกสาร " . $line['code'] . " จะถูกลบด้วย</span>";
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
			echo 'Please select item to remove!';
		}
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_remove_order", "Remove Order");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Remove", "fn.app.sales_bwd.order.remove()")
));
$modal->EchoInterface();

$dbc->Close();
