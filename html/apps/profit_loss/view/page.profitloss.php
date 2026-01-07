<table id="tblSales" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-white bg-dark text-center" colspan="9">ORDER</th>
		</tr>
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
		<tr>
			<td class="text-center bg-warning" colspan="9">
				จำนวน <span class="total"></span> บาท
			</td>
		</tr>
		<tr>
			<td class="text-center bg-secondary font-weight-bold" colspan="5"></td>
			<td class="text-right bg-secondary font-weight-bold">
				<span class="total_amountday font-weight-bold text-white"></span>
			</td>
			<td class="text-center bg-secondary font-weight-bold"></td>
			<td class="text-right text-white bg-secondary font-weight-bold">
				<span class="total_match font-weight-bold"></span> THB
			</td>
			<td class="text-center bg-secondary font-weight-bold"></td>
		</tr>
		<tr>
			<td class="text-center bg-info font-weight-bold" colspan="5"></td>
			<td class="text-right bg-info font-weight-bold">
				<span class="total_amountusdday font-weight-bold text-white"></span>
			</td>
			<td class="text-center bg-info font-weight-bold"></td>
			<td class="text-right text-white bg-info font-weight-bold">
				<span class="total_usdmatch font-weight-bold"></span> USD
			</td>
			<td class="text-center bg-info font-weight-bold"></td>
		</tr>
	</tfoot>
</table>
<div class=" mb-2">
	<button class="btn btn-primary" onclick="fn.app.profit_loss.profitloss.dialog_matchthb()">SUM THB</button>
	<button class="btn btn-dark" onclick="fn.app.profit_loss.profitloss.dialog_matchusd()">SUM USD</button>
</div>
<br>
<div class="row">
	<div class="col-6">
		<table id="tblPurchase" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="7">Purchase Spot (ทั้งก้อน)</th>
				</tr>
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
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-warning" colspan="7">
						จำนวน <span class="total_amount font-weight-bold"></span> กิโล <span class="total font-weight-bold"></span> USD
					</td>
				</tr>
			</tfoot>
		</table>

	</div>
	<div class="col-6">
		<div class="btn-area btn-group mb-2"></div>
		<table id="tblPurchaseSpot" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="8">Purchase Spot (ใช้จริง)</th>
				</tr>
				<tr>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Rate Spot</th>
					<th class="text-center">Rate Pmdc</th>
					<th class="text-center">Total </th>
					<th class="text-center">Currency </th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-secondary" colspan="2"></td>
					<td class="text-right bg-secondary">
						<span class="total_amount_thb font-weight-bold text-white"></span>
					</td>
					<td class="text-center bg-secondary" colspan="2"></td>
					<td class="text-right bg-secondary font-weight-bold text-white">
						<span class="total_thb font-weight-bold text-white"></span> THB
					</td>
					<td class="text-center bg-secondary" colspan="2"></td>
				</tr>
				<tr>
					<td class="text-center bg-info" colspan="2"></td>
					<td class="text-right bg-info">
						<span class="total_amount font-weight-bold text-white"></span>
					</td>
					<td class="text-center bg-info" colspan="2"></td>
					<td class="text-right bg-info font-weight-bold text-white">
						<span class="total font-weight-bold text-white"></span> USD
					</td>
					<td class="text-center bg-info" colspan="2"></td>
				</tr>
			</tfoot>
		</table>

	</div>
</div>
<br>
<div class="row">
	<div class="col-6">
	</div>
	<div class="col-6">
		<table id="tblRecheckUSD" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="3">Difference</th>
				</tr>
				<tr>
					<th class="text-center">Purchase Spot </th>
					<th class="text-center">Purchase USD </th>
					<th class="text-center">Difference</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-6">
		<table id="tblPurchaseUSD" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="8">Purchase USD (Physical) (ทั้งก้อน)</th>
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
	<div class="col-6">
		<div class="btn-area-usd btn-group mb-2"></div>
		<table id="tblPurchaseUSDtrue" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-white bg-dark text-center" colspan="8">Purchase USD (ใช้จริง)</th>
				</tr>
				<tr>
					<th class="text-center">Date</th>
					<th class="text-center">Bank</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Exchange Rate</th>
					<th class="text-center">Finance Rate</th>
					<th class="text-center">Type</th>
					<th class="text-center">Value (THB)</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center bg-info" colspan="2"></td>
					<td class="text-right bg-info text-white font-weight-bold">
						<span class="total_amount"></span> USD
					</td>
					<td class="text-center bg-info" colspan="3"></td>
					<td class="text-right bg-info text-white font-weight-bold">
						<span class="total_thb"></span> THB
					</td>
					<td class="text-center bg-info"></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-body">
					<table id="tblLoss" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
						<thead>
							<tr>
								<th class="text-white bg-dark text-center" colspan="7">กำไร / ขาดทุน</th>
							</tr>
							<tr>
								<th class="text-center">Orders</th>
								<th class="text-center">Purchase USD</th>
								<th class="text-center">Orders THB</th>
								<th class="text-center">Purchase THB</th>
								<th class="text-center">Profit USD</th>
								<th class="text-center">Profit THB</th>
								<th class="text-center">Total Profit</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>