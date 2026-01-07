<?php
	global $dbc;
	$today = time();
?>
<table id="tblReserve" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center">Date</th>
			<th class="text-center">Total Item</th>
			<th class="text-center">Kgs Locked</th>
			<th class="text-center">Actual Weight</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$sql = "SELECT DATE(lock_date),COUNT(id),SUM(weight_lock),SUM(weight_actual) FROM bs_reserve_silver GROUP BY DATE(lock_date)";
		$rst = $dbc->Query($sql);
		while($line = $dbc->Fetch($rst)){
			echo '<tr>';
				echo '<td class="text-center">'.$line[0].'</td>';
				echo '<td class="text-center">'.$line[1].'</td>';
				echo '<td class="text-right">'.number_format($line[2],4).'</td>';
				echo '<td class="text-right">'.number_format($line[3],4).'</td>';
			echo '</tr>';
			
		}
	
	?>
	</tbody>
</table>

