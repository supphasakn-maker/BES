<div class="btn-area btn-group mb-2"></div>
	<table id="tblTrading" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs" rowspan="2">
				<span type="checkall" control="chk_trading" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center" rowspan="2">Trade Date</th>
			<th class="text-center" rowspan="2">Supplier</th>
			<th class="text-center" rowspan="2">Type</th>
			<th class="text-center" colspan="3">Purchase</th>
			<th class="text-center" colspan="3">Sales</th>
			<th class="text-center" rowspan="2">Profit/Loss</th>
			<th class="text-center" rowspan="2">Action</th>
		</tr>
		<tr>
			
			<th class="text-center">Spot</th>
			<th class="text-center">Kgs</th>
			<th class="text-center">USD</th>
			<th class="text-center">Spot</th>
			<th class="text-center">Sgs</th>
			<th class="text-center">USD</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div class="row">
	<div class="col-12">
		<hr>
		<form name="adding" class="form-inline mr-2 " onsubmit="fn.app.trade_spot.trading.add();return false;">
			<label class="mr-sm-2">วันที่</label>
			<input name="date" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d");?>">
			<button type="submit" class="btn btn-danger">Match</button>
		</form>
		<hr>
	</div>
	<div class="col-6">
		<table id="tblPurchase" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
		<thead>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_spot" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Confirmed</th>
			<th class="text-center">Type</th>
			<th class="text-center">Supplier</th>
			<th class="text-center">Amount</th>
			<th class="text-center">Spot</th>
			<th class="text-center">Pm/Dc</th>
			<th class="text-center">Ref</th>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
	<div class="col-6">
		<table id="tblSales" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
		<thead>
			<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_spot" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">วันที่ขาย</th>
			<th class="text-center">ประเภท</th>
			<th class="text-center">ขายกับ</th>
			<th class="text-center">จำนวน</th>
			<th class="text-center">spot</th>
			<th class="text-center">ref</th>
			<th class="text-center">value_date</th>
			<th class="text-center">comment</th>
		</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
</div>
