<?php
	global $dbc;
?>
<div class="btn-area btn-group mb-2">
	<input name="bank_date" type="date" class="form-control" value="<?php echo date("Y-m-d")?>" >
	<select name="bank_id" class="form-control">
	<?php
		$sql = "SELECT * FROM bs_banks";
		$rst = $dbc->Query($sql);
		while($line = $dbc->Fetch($rst)){
			echo '<option value="'.$line['id'].'">'.$line['name'].':'.$line['number'].'</option>';
		}
	
	?>
	</select>
</div>

<table id="tblStatement" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_statement" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Date</th>
			<th class="text-center">Debit</th>
			<th class="text-center">Credit</th>
			<th class="text-center">Balance</th>
			<th class="text-center">Narrator</th>
			<th class="text-center">Link To</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
			<th class="text-right" colspan="2">Total</th>
			<th id="debit_total" class="text-center">0</th>
			<th id="credit_total" class="text-center">0</th>
			<th class="text-center" colspan="4"></th>
		</tr>
	</tfoot>
</table>
<form style="display:none;" name="import" enctype="multipart/form-data" >
	<input name="file" type="file">
</form>