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
		$items = isset($this->param['item']) ? $this->param['item'] : array();
		$removable = true;

		if (count($items) == 0) {
			$removable = false;
		}

		if ($removable) {
			echo '<ul>';
			foreach ($items as $item) {
				$trading = $dbc->GetRecord("bs_trade_spot ", "*", "id=" . $item);
				echo "<li>" . $trading['id'] . ' : ' . $trading['date'] . "</li>";
			}
			echo '</ul>';
		} else {
			echo 'Please selecte item to remove!';
		}
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_remove_trading", "Remove Trading");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Remove", "fn.app.trade_spot.trading.remove()")
));
$modal->EchoInterface();

$dbc->Close();
