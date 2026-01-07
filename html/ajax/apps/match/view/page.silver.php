
<table id="tblSilver" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_margin" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Date</th>
			<th class="text-center">Amount</th>
			<th class="text-center">Remark</th>
			<th class="text-center">Total Order</th>
			<th class="text-center">Total Amount</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div class=" mb-2">
	<button class="btn btn-primary" onclick="fn.app.match.silver.dialog_match()">Matching</button>
	<button class="btn btn-dark" onclick="fn.app.match.silver.clear_selection()">Clear Selection</button>
</div>
<div class="row">
	<div class="col-6">
		<table id="tblSales" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr><th class="text-white bg-dark text-center" colspan="9">Order</th></tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_sales" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Order ID</th>
					<th class="text-center">Date</th>
					<th class="text-center">Customer Name</th>
					<th class="text-center">Spot Rate</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Price</th>
					<th class="text-center">Total</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-warning" colspan="9">
						จำนวนคงเหลือ <span class="total_amount"></span> กิโลกรัม
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	
	<div class="col-6">
		<table id="tblPurchase" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr><th class="text-white bg-dark text-center" colspan="8">Purchase Spot</th></tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_purchase" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Rate Spot</th>
					<th class="text-center">Rate Pmdc</th>
					<th class="text-center">Total</th>
					<th class="text-center">Ref</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-warning" colspan="8">
						จำนวนคงเหลือ <span class="total_amount"></span> กิโลกรัม
					</td>
				</tr>
			</tfoot>
		</table>
		
	</div>
</div>
