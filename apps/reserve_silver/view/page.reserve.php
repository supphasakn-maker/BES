<?php
global $dbc;
$today = time();
?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", $today); ?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", $today + (86400 * 30)); ?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblReserve").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblReserve" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_reserve" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">IMPORT ID</th>
			<th class="text-center">Lock Date</th>
			<th class="text-center">Supplier</th>
			<th class="text-center">Kgs Locked</th>
			<th class="text-center">Already Fixed Rate</th>
			<th class="text-center">To Be Fixed</th>
			<th class="text-center">Locked Disc</th>
			<th class="text-center">Actual Weight</th>
			<th class="text-center">Defer</th>
			<th class="text-center">Bars</th>
			<th class="text-center">Type</th>
			<th class="text-center">Status</th>
			<th class="text-center">Brand</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>


<table class="table table-bordered table-form mt-2" width="100%">
	<tbody>
		<tr>
			<td class="p-2">ยอดทั้งหมด</td>
			<?php
			$reserve = $dbc->GetRecord("bs_reserve_silver", "FORMAT(SUM(weight_lock),4)", "import_id IS NULL");
			?>
			<td class="p-2">
				<div><?php echo $reserve[0]; ?></div>

			</td>
		</tr>

		<tr>
			<td class="p-2">ใช้ไปแล้ว (Actual Weight)</td>
			<?php
			$reserve = $dbc->GetRecord("bs_reserve_silver", "FORMAT(SUM(weight_actual),4)", "bs_reserve_silver.import_id IS NULL");
			?>
			<td class="p-2"><?php echo $reserve[0]; ?></td>
		</tr>
		<tr>
			<td class="p-2">ยังไม่ได้ระบุ Actual Weight</td>
			<?php
			$reserve = $dbc->GetRecord("bs_reserve_silver", "FORMAT(SUM(weight_lock),4)", "bs_reserve_silver.weight_actual IS NULL AND bs_reserve_silver.import_id IS NULL");
			?>
			<td class="p-2">
				<?php echo $reserve[0]; ?>
				<table class="table table-bordered mt-2" width="100%">
					<tbody>
						<?php
						$sql = "SELECT
						bs_suppliers.name AS supplier,
						FORMAT(SUM(weight_lock),4),
						bs_suppliers.comment AS comment
					FROM bs_suppliers 
					LEFT JOIN bs_reserve_silver ON bs_suppliers.id = bs_reserve_silver.supplier_id 
					WHERE bs_reserve_silver.weight_actual IS NULL AND bs_reserve_silver.import_id IS NULL AND bs_suppliers.id != 14
					GROUP BY bs_suppliers.id
					ORDER BY SUM(weight_lock) DESC
					";
						$rst = $dbc->Query($sql);
						while ($line = $dbc->Fetch($rst)) {
							if ($line[1] > 0) {
								echo '<tr>';
								echo '<td class="text-center">' . $line['supplier'] . '</td>';
								echo '<td class="text-right">' . $line[1] . '</td>';
								echo '<td class="text-left">' . $line['comment'] . '</td>';
								echo '</tr>';
							}
						}
						?>
					</tbody>
				</table>


			</td>
		</tr>
		<tr>
			<td class="p-2">ใช้ไปแล้ว(Actual Weight + Imported)</td>
			<?php
			$reserve = $dbc->GetRecord("bs_reserve_silver", "FORMAT(SUM(weight_lock),4)", "import_id IS NOT NULL");
			?>
			<td class="p-2"><?php echo $reserve[0]; ?></td>
		</tr>
	</tbody>
</table>