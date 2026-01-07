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
?>

	<table class="table table-sm table-striped table-bordered dt-responsive nowrap">
		<thead>
			<tr>
				<th class="text-center bg-dark text-white" colspan="5">ตาราง</th>
			</tr>
			<tr>
				<th class="text-center">รายการ</th>
				<th class="text-center">ขนาด</th>
				<th class="text-center">จำนวนที่ต้องการ</th>
				<th class="text-center">รวม</th>
				<th class="text-center">หมายุเหตุ</th>
			</tr>
		</thead>					
		<tbody>
		<?php
			$sql = "SELECT * FROM bs_stock_items WHERE prepare_id = ".$_POST['id'];
			
			$rst = $dbc->Query($sql);
			$total = 0;
			while($item = $dbc->Fetch($rst)){
				echo '<tr>';
					echo '<td class="text-center">'.$item['name'].'</td>';
					echo '<td class="text-right">'.number_format($item['size'],0).'</td>';
					echo '<td class="text-right">'.number_format($item['amount'],0).'</td>';
					echo '<td class="text-right">'.number_format($item['amount']*$item['size'],0).'</td>';
					echo '<td class="text-center">'.$item['comment'].'</td>';
					
				echo '</tr>';
				$total += ($item['amount']*$item['size']);
			}
		?>	
		</tbody>
		<tfoot>
			<tr>
				<td class="text-right" colspan="3">รวม</td>
				<td class="text-right"><?php echo number_format($total,4);?></td>
				<td></td>
			</tr>
		</tfoot>
	</table>	
	
<?php
	

	$dbc->Close();
?>
