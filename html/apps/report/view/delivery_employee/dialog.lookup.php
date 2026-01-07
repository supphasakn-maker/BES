<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	include_once "../../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;

			$sql = "SELECT 
				bs_deliveries_drivers.id AS id,
				bs_deliveries_drivers.emp_driver AS driver_id,
				bs_deliveries_drivers.truck_type AS truck_type,
				bs_deliveries_drivers.truck_license AS truck_license,
				bs_deliveries_drivers.time_departure AS time_departure,
				bs_deliveries_drivers.time_arrive AS time_arrive,
				bs_deliveries.id AS delivery_id,
				bs_deliveries.code AS delivery_code
			
			
			FROM bs_deliveries
			LEFT JOIN bs_deliveries_drivers ON bs_deliveries_drivers.delivery_id = bs_deliveries.id
			WHERE bs_deliveries_drivers.emp_driver = ".$this->param['driver_id']."
			AND DATE_FORMAT(bs_deliveries.delivery_date,'%Y-%m') = '".$this->param['month']."' 
			AND DATE_FORMAT(bs_deliveries.delivery_date,'%e') = ".$this->param['date'];


			echo '<table class="table table-sm table-bordered table-stripe">';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="text-center">ID</th>';
						echo '<th class="text-center">Truck Type</th>';
						echo '<th class="text-center">License</th>';
						echo '<th class="text-center">Departure</th>';
						echo '<th class="text-center">Arrive</th>';
						echo '<th class="text-center">Delivery ID</th>';
						echo '<th class="text-center">Delivery Code</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$rst = $dbc->Query($sql);
				while($line = $dbc->Fetch($rst)){
					echo '<tr>';
						echo '<td class="text-center">'.$line['id'].'</td>';
						echo '<td class="text-center">'.$line['truck_type'].'</td>';
						echo '<td class="text-center">'.$line['truck_license'].'</td>';
						echo '<td class="text-center">'.$line['time_departure'].'</td>';
						echo '<td class="text-center">'.$line['time_arrive'].'</td>';
						echo '<td class="text-center">'.$line['delivery_id'].'</td>';
						echo '<td class="text-center">'.$line['delivery_code'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';

		
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_lookup","Lookup");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
