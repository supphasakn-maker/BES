<table id="tblUSD" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_usd" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Date</th>
			<th class="text-center">Total Amount</th>
			<th class="text-center">Remark</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div class="mb-2">
	<button class="btn btn-primary" onclick="fn.app.match.usd.dialog_match()">Matching</button>
	<button class="btn btn-dark" onclick="fn.app.match.usd.clear_selection()">Clear Selection</button>
</div>
<div class="row">
	<div class="col-6">
		<table id="tblPurchaseSpot" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="9">Purchase Spot</th>
				</tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_purchase_spot" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Rate Spot</th>
					<th class="text-center">Rate Pmdc</th>
					<th class="text-center">Total (USD)</th>
					<th class="text-center">Ref</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-warning" colspan="9">
						จำนวนคงเหลือ <span class="total_amount"></span> USD
					</td>
				</tr>
			</tfoot>
		</table>
		<table id="tblPurchaseSpotMatched" class="d-none table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="8">Purchase Spot</th>
				</tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_purchase_spot_matched" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Rate Spot</th>
					<th class="text-center">Rate Pmdc</th>
					<th class="text-center">Total (USD)</th>
					<th class="text-center">Ref</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-warning" colspan="8">
						จำนวนคงเหลือ <span class="total_amount"></span> USD
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="col-6">
		<table id="tblPurchaseUSD" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="9">Purchase USD</th>
				</tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_purchase_usd" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Bank</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Exchange Rate</th>
					<th class="text-center">Finance Rate</th>
					<th class="text-center">Type</th>
					<th class="text-center">Value (THB)</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-warning" colspan="8">
						จำนวนคงเหลือ <span class="total_amount"></span> USD
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>