<?php
global $os;
$rate_difference = $os->load_variable("rate_difference");
$pmdc_rate       = $os->load_variable("pmdc_rate");
$change_buy      = $os->load_variable("change_buy");
$pmdc_grains     = $os->load_variable("pmdc_grains");
$insure_15     = $os->load_variable("insure_15");
$insure_50     = $os->load_variable("insure_50");
$insure_150    = $os->load_variable("insure_150");
?>

<style>
	/* ทำให้ตัวเลขปรับขนาดตามจอ */
	.metric-value {
		font-size: clamp(20px, 5vw, 40px);
		line-height: 1.1;
		margin: .25rem 0 .5rem;
	}

	/* เพิ่มพื้นที่ให้แตะง่ายบนจอเล็ก */
	.metric-item {
		padding: 14px 12px;
	}

	/* ให้ปุ่มเต็มความกว้างบนจอเล็ก และขนาดกำลังดี */
	@media (max-width: 767.98px) {

		/* < md = มือถือส่วนใหญ่ */
		.metric-btn {
			width: 100%;
		}
	}

	/* ให้การ์ดดูโปร่งบนจอใหญ่ */
	@media (min-width: 768px) {
		.metric-item {
			padding: 18px 16px;
		}
	}

	/* ปรับสี/เส้นนิดหน่อย */
	.metric-caption {
		color: #6c757d;
		/* text-secondary */
		font-size: 0.95rem;
	}
</style>

<ul class="list-group list-group-horizontal-md w-100">
	<li class="list-group-item flex-fill text-center metric-item">
		<div class="metric-caption">Rate Difference</div>
		<h1 class="metric-value">
			<strong id="exchange_rate_diff"><?php echo number_format($rate_difference, 3); ?></strong>
		</h1>
		<button class="btn btn-danger metric-btn"
			onclick="fn.app.announce.difference.dialog_edit()">CHANGE</button>
	</li>

	<li class="list-group-item flex-fill text-center metric-item">
		<div class="metric-caption">Premium / Discount</div>
		<h1 class="metric-value">
			<strong id="exchange_rate_pmdc"><?php echo number_format($pmdc_rate, 2); ?></strong>
		</h1>
		<button class="btn btn-danger metric-btn"
			onclick="fn.app.announce.difference.dialog_pmdc_change()">Change</button>
	</li>

	<li class="list-group-item flex-fill text-center metric-item">
		<div class="metric-caption">Change Buy</div>
		<h1 class="metric-value">
			<strong id="change_buy_val"><?php echo number_format($change_buy, 2); ?></strong>
		</h1>
		<button class="btn btn-danger metric-btn"
			onclick="fn.app.announce.difference.dialog_change_buy()">Change</button>
	</li>

	<li class="list-group-item flex-fill text-center metric-item">
		<div class="metric-caption">Premium / Discount Grains</div>
		<h1 class="metric-value">
			<strong id="pmdc_grains_val"><?php echo number_format($pmdc_grains, 2); ?></strong>
		</h1>
		<button class="btn btn-danger metric-btn"
			onclick="fn.app.announce.difference.dialog_pmdc_grains()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center metric-item">
		<div class="metric-caption">ค่าประกันแท่ง 15 กรัม</div>
		<h1 class="metric-value">
			<strong id="insure_15_val"><?php echo number_format($insure_15, 2); ?></strong>
		</h1>
		<button class="btn btn-danger metric-btn"
			onclick="fn.app.announce.difference.dialog_insure_15()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center metric-item">
		<div class="metric-caption">ค่าประกันแท่ง 50 กรัม</div>
		<h1 class="metric-value">
			<strong id="insure_50_val"><?php echo number_format($insure_50, 2); ?></strong>
		</h1>
		<button class="btn btn-danger metric-btn"
			onclick="fn.app.announce.difference.dialog_insure_50()">Change</button>
	</li>
	<li class="list-group-item flex-fill text-center metric-item">
		<div class="metric-caption">ค่าประกันแท่ง 150 กรัม</div>
		<h1 class="metric-value">
			<strong id="insure_150_val"><?php echo number_format($insure_150, 2); ?></strong>
		</h1>
		<button class="btn btn-danger metric-btn"
			onclick="fn.app.announce.difference.dialog_insure_150()">Change</button>
	</li>
</ul>