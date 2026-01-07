<div class="btn-area btn-group mb-2"></div>
<div class="row">
	<div class="col-6">
		<table id="tblPurchase" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center table-dark" colspan="11">Buy Side</th>
				</tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_purchase" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Spot</th>
					<th class="text-center">Pmdc</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Spot Value</th>
					<th class="text-center">Discount Value</th>
					<th class="text-center">Net Spot Value</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<div class="col-6">
		<table id="tblAdjusted" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center">Date</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Purchase</th>
					<th class="text-center">Sales</th>
					<th class="text-center">New</th>
					<th class="text-center">Profit From Trade</th>
					<th class="text-center">Ajust Cost</th>
					<th class="text-center">Ajust Discount</th>
					<th class="text-center">Net Profit</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
	
<div class="row">
	<div class="col-12">
		<hr>
		<form name="adding" class="form-inline mr-2 " onsubmit="fn.app.adjust_cost.adjust.add();return false;">
			<button  type="button" onclick="fn.app.adjust_cost.adjust.calcuate()" class="btn btn-warning mr-2">Calcuate</button>
			 <label class="mr-sm-2">วันที่</label>
			<input name="date" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d");?>">
			<button type="submit" class="btn btn-danger">Match</button>
			
		</form>
		<hr>
		<div>
			<table class="table table-form table-sm table-bordered">
				<tbody>
					<tr>
						<th>1. ยอดส่วนต่างระหว่างราคาซื้อครั้งเก่าและใหม่ (PO เก่า - PO ใหม่ รวม discount)</th>
						<td><input class="form-control form-control-sm text-center" name="value_a" value="0" readonly></td>
						<th>Profit from trade at standard</th>
						<td><input class="form-control form-control-sm text-center" name="value_profit" value="0" readonly></td>
					</tr>
					<tr>
						<th>2. กำไรขาดทุนที่เกิดจากการ adj cost (Sell - PO ใหม่ ไม่รวม discount)</th>
						<td><input class="form-control form-control-sm text-center" name="value_b" value="0" readonly></td>
						<th>Cost decrease(increase) from adj cost (PO เก่า - PO ใหม่ ไม่รวม discount)</th>
						<td><input class="form-control form-control-sm text-center" name="cost_a" value="0" readonly></td>
					</tr>
					<tr>
						<th>3. กำไรขาดทุนเพิ่มจากการ discount (discount ใหม่ - discount เก่า)</th>
						<td><input class="form-control form-control-sm text-center" name="value_c" value="0" readonly></td>
						<th>Cost decrease(increase) from adj discount</th>
						<td><input class="form-control form-control-sm text-center" name="cost_b" value="0" readonly></td>
					</tr>
					<tr>
						<th>4. ยอด 1+2+3</th>
						<td><input class="form-control form-control-sm text-center" name="value_d" value="0" readonly></td>
						<th>Net profit</th>
						<td><input class="form-control form-control-sm text-center" name="value_netprofit" value="0" readonly></td>
					</tr>
					<tr>
						<th>Sell-PO เก่าไม่รวม discount</th>
						<td><input class="form-control form-control-sm text-center" name="value_e" value="0" readonly></td>
						<th></th>
						<td></td>
					</tr>
				</tbody>
				
			</table>
		</div>
		<hr>
	</div>
	<div class="col-6">
		<table id="tblSales" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center table-dark" colspan="11">Sell Side</th>
				</tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_spot" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Spot</th>
					<th class="text-center">Pmdc</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Spot Value</th>
					<th class="text-center">Discount Value</th>
					<th class="text-center">Net Spot Value</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<div class="col-6">
		<table id="tblPurchaseNew" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center table-dark" colspan="10">Buy Side</th>
				</tr>
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_new" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Spot</th>
					<th class="text-center">Pmdc</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Spot Value</th>
					<th class="text-center">Discount Value</th>
					<th class="text-center">Net Spot Value</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
