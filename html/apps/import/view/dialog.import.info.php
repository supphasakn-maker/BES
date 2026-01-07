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
		$import = $dbc->GetRecord("bs_imports", "*", "id=" . $this->param['id']);

?>

		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab_info_a_menu" data-toggle="tab" href="#tab_info_a" role="tab" aria-controls="tab_info_a" aria-selected="true">COA</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab_info_b_menu" data-toggle="tab" href="#tab_info_b" role="tab" aria-controls="tab_info_b" aria-selected="false">Reserve</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab_info_c_menu" data-toggle="tab" href="#tab_info_c" role="tab" aria-controls="tab_info_c" aria-selected="false">Purchase Spot</a>
			</li>
		</ul>
		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="tab_info_a" role="tabpanel" aria-labelledby="home-tab">
				<?php
				if ($import['info_coa_files'] != null) {
					$json = json_decode($import['info_coa_files'], true);
					echo '<ul class="list-group">';
					foreach ($json as $item) {
						echo '<li class="list-group-item"><a href="' . $item . '" target="_blank">' . $item . '<a></li>';
					}
					echo '</ul>';
				} else {
					echo '<div class="alert alert-warning">ยังไม่ได้ Uplaod Files</div>';
				}
				?>
			</div>
			<div class="tab-pane fade" id="tab_info_b" role="tabpanel" aria-labelledby="profile-tab">
				<?php
				echo '<table class="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">Lock Date</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">Type</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$sql = "SELECT * FROM bs_reserve_silver WHERE import_id =" . $import['id'];
				$rst = $dbc->Query($sql);
				while ($reserve = $dbc->Fetch($rst)) {
					echo '<tr>';
					echo '<td class="text-center">' . $reserve['lock_date'] . '</td>';
					echo '<td class="text-center">' . $reserve['supplier_id'] . '</td>';
					echo '<td class="text-center">' . $reserve['weight_lock'] . '</td>';
					echo '<td class="text-center">' . $reserve['type'] . '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';

				?>
			</div>
			<div class="tab-pane fade" id="tab_info_c" role="tabpanel" aria-labelledby="profile-tab">
				<?php
				echo '<table class="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">Date</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">Spot</th>';
				echo '<th class="text-center">Pm/Dc</th>';
				echo '<th class="text-center">Ref</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$sql = "SELECT * FROM bs_purchase_spot WHERE import_id =" . $import['id'];
				$rst = $dbc->Query($sql);
				while ($purchase = $dbc->Fetch($rst)) {
					echo '<tr>';
					echo '<td class="text-center">' . $purchase['date'] . '</td>';
					echo '<td class="text-center">' . $purchase['supplier_id'] . '</td>';
					echo '<td class="text-center">' . $purchase['amount'] . '</td>';
					echo '<td class="text-center">' . $purchase['rate_spot'] . '</td>';
					echo '<td class="text-center">' . $purchase['rate_pmdc'] . '</td>';
					echo '<td class="text-center">' . $purchase['ref'] . '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				?>
			</div>
		</div>
<?php
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setExtraClass("modal-lg");
$modal->setModel("dialog_info_import", "รายละเอียด");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
?>