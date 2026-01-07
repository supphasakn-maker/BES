
<?php
$today = time();
?>

<div class="card mt-2">
	<div class="card-body">
		<div class="btn-group mb-2">
			<form name="filter" class="form-inline mr-2" onsubmit="return false;">
				<label class="mr-sm-2">From</label>
				<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*90));?>">
				<label class="mr-sm-2">To</label>
				<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today);?>">
				<button type="button" class="btn btn-primary" onclick='$("#tblDailyTable").DataTable().draw();'>Lookup</button>
			</form>
		</div>
		<ul class="nav nav-tabs" id="HistoryTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="order-history-tab" data-toggle="tab" href="#order-history" role="tab" aria-controls="order-history" aria-selected="true">ประวัตการสั่งซื้อ</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="pending-delivery-tab" data-toggle="tab" href="#pending-delivery" role="tab" aria-controls="pending-delivery" aria-selected="false">ประวัติค้างส่ง</a>
			</li>
		</ul>
		<div class="tab-content" id="HistoryTabContent">
			<div class="tab-pane fade show active" id="order-history" role="tabpanel" aria-labelledby="order-history-tab">
				<table id="tblDailyTable" class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
					<thead class="bg-dark">
						<tr>
							<th class="text-center text-white">PO</th>
							<th class="text-center text-white">DATE ADD</th>
							<th class="text-center text-white">DELIVERY DATE</th>
							<th class="text-center text-white">KGS.</th>
							<th class="text-center text-white">BATH / KGS.</th>
							<th class="text-center text-white">TOTAL</th>
							<th class="text-center text-white">VAT</th>
							<th class="text-center text-white">TOTAL +VAT</th>
							<th class="text-center text-white">SALES</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tbody>
						<tr>
							<th class="text-right" colspan="3">ยอดรวม</th>
							<th class="text-center" xname="tAmount"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="tab-pane fade" id="pending-delivery" role="tabpanel" aria-labelledby="pending-delivery-tab">
				<table id="tblDailyRemain" class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
					<thead class="bg-dark">
						<tr>
							<th class="text-center text-white">PO</th>
							<th class="text-center text-white">DATE ADD</th>
							<th class="text-center text-white">DELIVERY DATE</th>
							<th class="text-center text-white">KGS.</th>
							<th class="text-center text-white">BATH / KGS.</th>
							<th class="text-center text-white">SALES</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tbody>
						<tr>
							<th class="text-right" colspan="3">ยอดรวม</th>
							<th class="text-center"  xname="tAmount"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		
		
	</div>
</div>