<?php
global $os;
$rate_difference = $os->load_variable("rate_difference");
$pmdc_rate = $os->load_variable("pmdc_rate");
$change_buy = $os->load_variable("change_buy");
$pmdc_grains = $os->load_variable("pmdc_grains");

?>
<ul class="list-group list-group-horizontal">
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Rate Difference</div>
		<h1><strong id="exchange_rate"><?php echo number_format($rate_difference, 3); ?></strong></h1>
		<button class="btn btn-danger" onclick="fn.app.announce.difference.dialog_edit()">CHANGE</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Premium / Discount</div>
		<h1><strong id="exchange_rate"><?php echo number_format($pmdc_rate, 2); ?></strong></h1>
		<button class="btn btn-danger" onclick="fn.app.announce.difference.dialog_pmdc_change()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Change Buy</div>
		<h1><strong id="exchange_rate"><?php echo number_format($change_buy, 2); ?></strong></h1>
		<button class="btn btn-danger" onclick="fn.app.announce.difference.dialog_change_buy()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">Premium / Discount Grains</div>
		<h1><strong id="exchange_rate"><?php echo number_format($pmdc_grains, 2); ?></strong></h1>
		<button class="btn btn-danger" onclick="fn.app.announce.difference.dialog_pmdc_grains()">Change</button>
	</li>
</ul>