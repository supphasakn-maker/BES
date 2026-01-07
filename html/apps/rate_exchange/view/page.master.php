<?php
global $os;
$scb_rate = $os->load_variable("scb_rate");
$kbank_rate = $os->load_variable("kbank_rate");
$bbl_rate = $os->load_variable("bbl_rate");
$bay_rate = $os->load_variable("bay_rate");

?>
<ul class="list-group list-group-horizontal">
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">SCB RATE</div>
		<h1><strong id="exchange_rate"><?php echo number_format($scb_rate, 4); ?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.rate_exchange.master.dialog_change_scb()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">KBANK RATE</div>
		<h1><strong id="exchange_rate"><?php echo number_format($kbank_rate, 4); ?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.rate_exchange.master.dialog_change_kbank()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">BBL RATE</div>
		<h1><strong id="exchange_rate"><?php echo number_format($bbl_rate, 4); ?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.rate_exchange.master.dialog_change_bbl()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center">
		<div class="text-secondary">BAY RATE</div>
		<h1><strong id="exchange_rate"><?php echo number_format($bay_rate, 4); ?></strong></h1>
		<button class="btn btn-warning" onclick="fn.app.rate_exchange.master.dialog_change_bay()">Change</button>
	</li>
</ul>