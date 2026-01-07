<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	$start = $_POST['from'];
	$end = $_POST['to'];
	
	switch($_POST['groupby']){
		case "none":
			echo '<table id="tblReport" class="table table-striped table-bordered table-hover table-middle table-sm" width="100%">';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="text-center">ID</th>';
						echo '<th class="text-center">Datetime</th>';
						echo '<th class="text-center">Action</th>';
						echo '<th class="text-center">User</th>';
						echo '<th class="text-center">Value</th>';
						echo '<th class="text-center">IP Address</th>';
						echo '<th class="text-center">Action</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$sql = "SELECT * FROM os_logs WHERE datetime BETWEEN '$start 00:00:00' AND '$end 23:59:59'";
				$rst = $dbc->Query($sql);
				while($log = $dbc->Fetch($rst)){
					echo '<tr>';
						echo '<td class="text-center">'.$log['id'].'</td>';
						echo '<td class="text-center">'.$log['datetime'].'</td>';
						echo '<td class="text-center">'.$log['user'].'</td>';
						echo '<td class="text-center">'.$log['action'].'</td>';
						echo '<td class="text-center">'.$log['value'].'</td>';
						echo '<td class="text-center">'.$log['location'].'</td>';
						echo '<td class="text-center">';
							echo '<button type="button" class="btn btn-xs btn-outline-dark btn-icon" onclick="fn.app.logger.log.dialog_view('.$log['id'].')">';
								echo '<i class="fa fa-eye"></i>';
							echo '</button>';
						echo '</td>';
						echo '';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
			break;
		case "user":
			echo '<table id="tblReport" class="table table-striped table-bordered table-hover table-middle" width="100%">';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="text-center">User ID</th>';
						echo '<th class="text-center">Username</th>';
						echo '<th class="text-center">Total</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$sql = "SELECT user,COUNT(id) as items FROM os_logs WHERE datetime BETWEEN '$start 00:00:00' AND '$end 23:59:59' GROUP BY user";
				$rst = $dbc->Query($sql);
				while($log = $dbc->Fetch($rst)){
					$user = $dbc->GetRecord('users',"*","id=".$log['user']);
					echo '<tr>';
						echo '<td>'.$log['user'].'</td>';
						echo '<td>'.$user['name'].'</td>';
						echo '<td>'.$log['items'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
			break;
		case "action":
			echo '<table id="tblReport" class="table table-striped table-bordered table-hover table-middle" width="100%">';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="text-center">Action</th>';
						echo '<th class="text-center">Total</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$sql = "SELECT action,COUNT(id) as items FROM os_logs WHERE datetime BETWEEN '$start 00:00:00' AND '$end 23:59:59' GROUP BY action";
				$rst = $dbc->Query($sql);
				while($log = $dbc->Fetch($rst)){
					//$user = $dbc->GetRecord('users',"*","id=".$log['user']);
					echo '<tr>';
						echo '<td class="text-center">'.$log['action'].'</td>';
						echo '<td class="text-center">'.$log['items'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
			break;
			break;
	}
	
	
	
	
	

	$dbc->Close();
?>