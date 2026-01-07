<?php
	$today = time();
?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*90));?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today);?>">
		<?php
			$ui_form->EchoItem(array(
				"type" => "comboboxdatabank",
				"class" => "mr-sm-2",
				"source" => "db_bank",
				"name" => "bank",
				"default" => array(
					"value" => "none",
					"name" => "เลือกธนาคาร"
				)
			));
			
		?>
		<select name="order_by" class="form-control">
			<option value="bs_transfers.date">เรียงตามวันที่</value>
			<option value="bs_transfers.remark">เรียงตาม Remark</value>
		</select>
		<select name="order_type" class="form-control mr-sm-2">
			<option value="ASC">จากน้อยไปมาก</value>
			<option value="DESC">จากมากไปน้อย</value>
		</select>
		<button type="button" class="btn btn-primary" onclick='fn.app.trust_receipt.tr.load();'>Lookup</button>
		<div class="ml-2 custom-control custom-checkbox">
		  <input name="chkShowCompleted" type="checkbox" class="custom-control-input" id="chkShowCompleted" onclick='fn.app.trust_receipt.tr.load();'>
		  <label class="custom-control-label" for="chkShowCompleted">Show Completed</label>
		</div>
	</form>
</div>
<div id="tr_zone" class="card p-4">
	
	<table id="tblTR" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
		<thead>
			<tr>
				<th class="text-center hidden-xs">
					<span type="checkall" control="chk_spot" class="far fa-lg fa-square"></span>
				</th>
				<th class="text-center">TR Date</th>
				<th class="text-center">Type</th>
				<th class="text-center">TR ID</th>
				<th class="text-center">TR Number</th>
				<th class="text-center">Interest</th>
				<th class="text-center">USD</th>
				<th class="text-center">THB</th>
				<th class="text-center">Supplier</th>
				<th class="text-center">Due Date</th>
				<th class="text-center">Payment Date</th>
				<th class="text-center">Interest Day</th>
				<th class="text-center">Counter Rate</th>
				<th class="text-center">USD Paid</th>
				<th class="text-center">THB Paid</th>
				<th class="text-center">Interest</th>
				<th class="text-center">Total Interest</th>
				<th class="text-center">Action</th>
			</tr>
		</thead>
		
		<tbody>
		</tbody>
		
	</table>
</div>