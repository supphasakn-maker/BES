<?php
	global $os;
	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");
	$rate_pmdc_purchase = $os->load_variable("rate_pmdc_purchase");

	$rate_pmdc_recycle = $os->load_variable("rate_pmdc_recycle");

	$rate_recycle1 = $os->load_variable("rate_recycle1");
	$rate_recycle2 = $os->load_variable("rate_recycle2");

?>
<ul class="list-group list-group-horizontal">
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Exchange Rate</div>
		<h1><strong id="exchange_rate"><?php echo number_format($rate_exchange,3);?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.datapanel.master.dialog_change_exchange()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Spot Rate</div>
		<h1><strong id="exchange_rate"><?php echo number_format($rate_spot,3);?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.datapanel.master.dialog_change_spot()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Premium/Discount Sales</div>
		<div class="text-secondary">Sales</div>
		<h1><strong id="exchange_rate"><?php echo number_format($rate_pmdc,2);?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.datapanel.master.dialog_change_pmdc()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Premium/Discount</div>
		<div class="text-secondary">Purchase</div>
		<h1><strong id="exchange_rate"><?php echo number_format($rate_pmdc_purchase,2);?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.datapanel.master.dialog_change_pmdc_purchase()">Change</button>
	</li>
</ul>
<ul class="list-group list-group-horizontal">
<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Premium/Discount Recycle</div>
		<h1><strong id="exchange_rate"><?php echo number_format($rate_pmdc_recycle,3);?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.datapanel.master.dialog_change_pmdc_recycle()">Change</button>
</li>
<li class="list-group-item flex-fill text-center">
	<div class="text-secondary">Recycle1</div>
	<h1><strong id="exchange_rate"><?php echo number_format($rate_recycle1,3);?></strong></h1>
	<button class="btn btn-warning" onclick="fn.app.datapanel.master.dialog_change_rate_recycle()">Change</button>
</li>
<li class="list-group-item flex-fill text-center">
	<div class="text-secondary">Recycle2</div>
	<h1><strong id="exchange_rate"><?php echo number_format($rate_recycle2,3);?></strong></h1>
	<button class="btn btn-warning" onclick="fn.app.datapanel.master.dialog_change_rate_recycle2()">Change</button>
</li>
<li class="list-group-item flex-fill text-center">
</li>
</ul>