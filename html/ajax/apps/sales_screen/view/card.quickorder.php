<div class="card mb-3">
	<div class="card-header border-0">
		<h6>Sales (Quick)</h6>
	</div>
	<div class="card-body">
		<div>
			<form name="form_addquick_order" onsubmit="fn.app.sales.quick_order.add();return false;">
				<div class="input-group">
					<select name="customer_id" class="form-control select2" placeholder="โปรดเลือกลูกค้า">
							<option value="">โปรดเลือกลูกค้า</option>
						<?php
							$sql = "SELECT * FROM bs_customers";
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								echo '<option value="'.$line['id'].'">'.$line['name'].'</option>';
							}
						?>
					</select>
					<input type="number" step=".0001" name="amount" class="form-control" placeholder="จำนวน">
					<input name="price" class="form-control" placeholder="ราคา">
					<select name="vat_type" class="form-control">
						<option value="0">ไม่มีภาษี</option>
						<option value="2">7%</option>
					</select>
				</div>
				<div class="input-group">
					<select name="product_id" class="form-control" placeholder="โปรดเลือกลูกค้า">
						
						<?php
							$sql = "SELECT * FROM bs_products";
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								echo '<option value="'.$line['id'].'">'.$line['name'].'</option>';
							}
						?>
					</select>
					<input name="rate_spot" class="form-control" placeholder="spot">
					<input name="rate_exchange" class="form-control" placeholder="exchange">
					<input name="remark" type="text" class="form-control" placeholder="remark">
					<div class="input-group-append">
						</div>
				</div>
				<button class="btn btn-block btn-dark" type="submit">Add</button>
					
			</form>
		</div>
		<table id="tblQuickOrder" class="datatable table table-striped table-sm table-bordered nowrap">
			<!-- Filter columns -->
			<thead>
				<tr>
					<th class="text-center">วันที่/เวลา</th>
					<th class="text-center">ชื่อลูกค้า</th>
					<th class="text-center">จำนวน</th>
					<th class="text-center">ราคา</th>
					<th class="text-center">สถานะ</th>
					<th class="text-center">spot</th>
					<th class="text-center">exchange</th>
					<th class="text-center">remark</th>
					<th class="text-center">sales</th>
					<th class="text-center">สินค้า</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tbody>
				<tr>
					<th class="text-right" colspan="2">ยอดรวม</th>
					<th class="text-center" xname="tAmount"></th>
					<th class="text-right">รวมทั้งหมด</th>
					<th class="text-center" colspan="4" xname="tValue"></th>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>