<div class="card mb-3">
	<div class="card-header border-0">
		<h6>Sales Back(Quick Buy Silver BAR)</h6>
	</div>
	<div class="card-body">
		<div>
			<form name="form_addquickbuy_order"  onsubmit="fn.app.sales_silver.quick_buyorder.add();return false;">
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
                        <option value="2">แท่งเงิน</option>
					</select>
					<input name="rate_spot" class="form-control" placeholder="spot">
					<input name="rate_exchange" class="form-control" placeholder="exchange">
					<input name="remark" type="text" class="form-control" placeholder="remark">
					<div class="input-group-append">
						</div>
				</div>
				<button class="btn btn-block btn-danger" type="submit">Add</button>
					
			</form>
			</div>
		<table id="tblBuyOrder" class="datatable table table-striped table-sm table-bordered nowrap">
			<!-- Filter columns -->
			<thead class="bg-danger">
				<tr>
					<th class="text-center text-white"><?php echo date('Y-m-d');?></th>
					<th class="text-center text-white">PO</th>
					<th class="text-center text-white">CUSTOMER</th>
					<th class="text-center text-white">KGS.</th>
					<th class="text-center text-white">PRICE</th>
					<th class="text-center text-white">STATUS</th>
					<th class="text-center text-white">SP</th>
					<th class="text-center text-white">EX</th>
					<th class="text-center text-white">SALES</th>
					<th class="text-center text-white">PRODUCT</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tbody>
				<tr>
					<th class="text-right" colspan="3">ยอดรวม</th>
					<th class="text-center" xname="tAmount"></th>
					<th class="text-center" colspan="7"></th>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>