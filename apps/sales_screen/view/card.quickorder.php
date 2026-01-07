<div class="card mb-3">
	<div class="card-header border-0">
		<h6>Sales (Quick)</h6>
	</div>
	<div class="card-body">
		<div>
			<form name="form_addquick_order" onsubmit="fn.app.sales.quick_order.add();return false;">
				<!-- Customer and Product Row -->
				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label">Customer</label>
						<select name="customer_id" class="form-control select2">
							<option value="">Select Customer</option>
							<?php
							$sql = "SELECT * FROM bs_customers";
							$rst = $dbc->Query($sql);
							while ($line = $dbc->Fetch($rst)) {
								echo '<option value="' . $line['id'] . '">' . $line['name'] . '</option>';
							}
							?>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label">Product</label>
						<select name="product_id" class="form-control">
							<?php
							$sql = "SELECT * FROM bs_products WHERE id NOT IN (9, 11)";
							$rst = $dbc->Query($sql);
							while ($line = $dbc->Fetch($rst)) {
								echo '<option value="' . $line['id'] . '">' . $line['name'] . '</option>';
							}
							?>
						</select>
					</div>
				</div>

				<!-- Amount and Pricing Row -->
				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label">Amount (KGS)</label>
						<input type="number" step=".0001" name="amount" class="form-control" placeholder="0.0000" autocomplete="off">
					</div>
					<div class="col-md-4">
						<label class="form-label">Price</label>
						<input type="text" name="price" class="form-control" placeholder="Price" autocomplete="off">
					</div>
					<div class="col-md-4">
						<label class="form-label">USD Price</label>
						<input type="text" name="price_usd" class="form-control" placeholder="USD Price" value="0" autocomplete="off">
					</div>

				</div>

				<!-- VAT and Rates Row -->
				<div class="row mb-3">
					<div class="col-md-3">
						<label class="form-label">VAT Type</label>
						<select name="vat_type" class="form-control">
							<option value="0">No VAT</option>
							<option value="2">7% VAT</option>
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Spot Rate</label>
						<input name="rate_spot" class="form-control" placeholder="Spot Rate" autocomplete="off">
					</div>
					<div class="col-md-3">
						<label class="form-label">Exchange Rate</label>
						<input name="rate_exchange" class="form-control" placeholder="Exchange Rate" autocomplete="off">
					</div>
					<div class="col-md-3">
						<label class="form-label">Store</label>
						<select name="store" class="form-control">
							<option value="">Select Store</option>
							<option value="BWS">BWS</option>
							<option value="SILVERNOW">SILVER NOW</option>
							<option value="LG">LUCK GEMS</option>
							<option value="EXHIBITION">EXHIBITION</option>
						</select>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label">รูปแบบการจัดส่ง</label>
						<select name="orderable_type" class="form-control">
							<option value="">Select Delivery</option>
							<option value="delivered_by_company">จัดส่งโดยรถบริษัท</option>
							<option value="post_office">จัดส่งโดยไปรษณีย์ไทย</option>
							<option value="receive_at_company">รับสินค้าที่บริษัท</option>
							<option value="receive_at_luckgems">รับสินค้าที่ Luck Gems</option>
							<option value="delivered_by_transport">จัดส่งโดยขนส่ง</option>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label">Remark</label>
						<input name="remark" type="text" class="form-control" placeholder="Additional notes...">
					</div>
				</div>

				<button class="btn btn-block btn-dark mt-3" type="submit">Add Order</button>
			</form>
		</div>

		<!-- Table remains the same -->
		<table id="tblQuickOrder" class="datatable table table-striped table-sm table-bordered nowrap mt-4">
			<!-- Filter columns -->
			<thead class="bg-dark">
				<tr>
					<th class="text-center text-white"><?php echo date('Y-m-d'); ?></th>
					<th class="text-center text-white">PO</th>
					<th class="text-center text-white">CUSTOMER</th>
					<th class="text-center text-white">KGS.</th>
					<th class="text-center text-white">PRICE</th>
					<th class="text-center text-white">PRICE+VATS</th>
					<th class="text-center text-white">STATUS</th>
					<th class="text-center text-white">SP</th>
					<th class="text-center text-white">EX</th>
					<th class="text-center text-white">SALES</th>
					<th class="text-center text-white">USD</th>

				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<th class="text-right" colspan="3">Total</th>
					<th class="text-center" xname="tAmount"></th>
					<th class="text-right">Grand Total</th>
					<th class="text-right" xname="tValue"></th>
					<th class="text-center" colspan="5"></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>