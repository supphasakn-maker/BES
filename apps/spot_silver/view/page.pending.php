<?php
global $ui_form, $os;

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");
$rate_pmdc_purchase = $os->load_variable("rate_pmdc_purchase");
?>
<style>
	:root {
		--primary-color: #00204E;
		--primary-gradient: linear-gradient(135deg, #00204E 0%, #003366 100%);
		--secondary-color: #003366;
		--accent-color: #0056b3;
		--light-bg: #f8f9fa;
		--border-color: #e9ecef;
		--text-muted: #6c757d;
		--success-color: #28a745;
		--warning-color: #ffc107;
		--danger-color: #dc3545;
	}

	.purchase-container {
		background: #fff;
		border-radius: 12px;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
		overflow: hidden;
		margin-bottom: 2rem;
	}

	.form-section {
		background: var(--light-bg);
		padding: 2rem;
		border-right: 1px solid var(--border-color);
		height: 100%;
	}

	.form-header {
		background: var(--primary-gradient);
		color: white;
		padding: 1.5rem 2rem;
		margin: -2rem -2rem 2rem -2rem;
		border-radius: 12px 12px 0 0;
	}

	.form-header h4 {
		margin: 0;
		font-weight: 600;
		font-size: 1.0rem;
		display: flex;
		align-items: center;
	}

	.form-header i {
		margin-right: 12px;
		background: rgba(255, 255, 255, 0.2);
		padding: 8px;
		border-radius: 6px;
	}

	.table-form {
		background: white;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
		border: none;
		overflow: hidden;
	}

	.table-form tbody tr {
		border: none;
		transition: background-color 0.2s ease;
	}

	.table-form tbody tr:hover {
		background-color: rgba(0, 32, 78, 0.02);
	}

	.table-form td {
		border: none;
		padding: 1.5rem 1.2rem;
		vertical-align: middle;
		border-bottom: 1px solid #f0f0f0;
	}

	.table-form td:first-child {
		background: linear-gradient(90deg, rgba(0, 32, 78, 0.05) 0%, rgba(0, 32, 78, 0.02) 100%);
		font-weight: 600;
		color: var(--primary-color);
		width: 180px;
		border-right: 2px solid rgba(0, 32, 78, 0.1);
	}

	.table-form label {
		margin: 0;
		font-size: 0.9rem;
		color: var(--primary-color);
		font-weight: 600;
	}

	.form-control,
	.form-select {
		border: 2px solid #e9ecef;
		border-radius: 6px;
		padding: 0.75rem 1rem;
		font-size: 0.95rem;
		transition: all 0.3s ease;
		background: white;
	}

	.form-control:focus,
	.form-select:focus {
		border-color: var(--accent-color);
		box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
		outline: none;
	}

	.submit-btn {
		background: var(--primary-gradient);
		color: white;
		border: none;
		padding: 16px 24px;
		border-radius: 8px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		width: 100%;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		min-height: 56px;
		margin-top: 24px;
		box-shadow: 0 4px 12px rgba(0, 32, 78, 0.3);
	}

	.submit-btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(0, 32, 78, 0.4);
	}

	.submit-btn:active {
		transform: translateY(0);
	}

	.data-section {
		background: white;
		padding: 2rem;
	}

	.data-header {
		background: var(--primary-gradient);
		color: white;
		padding: 1.5rem 2rem;
		margin: -2rem -2rem 2rem -2rem;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.data-header h4 {
		margin: 0;
		font-weight: 600;
		font-size: 1.0rem;
		display: flex;
		align-items: center;
	}

	.data-header i {
		margin-right: 12px;
		background: rgba(255, 255, 255, 0.2);
		padding: 8px;
		border-radius: 6px;
	}

	.data-stats {
		display: flex;
		gap: 1rem;
		font-size: 0.9rem;
		opacity: 0.9;
	}

	.data-stats span {
		background: rgba(255, 255, 255, 0.15);
		padding: 4px 8px;
		border-radius: 4px;
	}

	#tblPending {
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
		border: none;
	}

	#tblPending thead {
		background: var(--primary-gradient) !important;
	}

	#tblPending thead th {
		border: none;
		font-weight: 600;
		font-size: 0.9rem;
		padding: 1rem 0.75rem;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		position: relative;
	}

	#tblPending thead th:not(:last-child)::after {
		content: '';
		position: absolute;
		right: 0;
		top: 25%;
		height: 50%;
		width: 1px;
		background: rgba(255, 255, 255, 0.2);
	}

	#tblPending tbody tr {
		transition: all 0.2s ease;
		border: none;
	}

	#tblPending tbody tr:hover {
		background-color: rgba(0, 32, 78, 0.03);
		transform: scale(1.001);
	}

	#tblPending tbody td {
		border: none;
		padding: 1rem 0.75rem;
		border-bottom: 1px solid #f0f0f0;
		font-size: 0.9rem;
	}

	#tblPending tbody tr:last-child td {
		border-bottom: none;
	}

	.currency-badge {
		display: inline-block;
		padding: 4px 8px;
		border-radius: 4px;
		font-size: 0.8rem;
		font-weight: 600;
		text-transform: uppercase;
	}

	.currency-usd {
		background: rgba(40, 167, 69, 0.1);
		color: var(--success-color);
		border: 1px solid rgba(40, 167, 69, 0.2);
	}

	.currency-thb {
		background: rgba(0, 86, 179, 0.1);
		color: var(--accent-color);
		border: 1px solid rgba(0, 86, 179, 0.2);
	}

	.type-badge {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 4px;
		font-size: 0.8rem;
		font-weight: 500;
		text-transform: capitalize;
	}

	.type-physical {
		background: rgba(40, 167, 69, 0.1);
		color: var(--success-color);
	}

	.type-stock {
		background: rgba(0, 86, 179, 0.1);
		color: var(--accent-color);
	}

	.type-trade {
		background: rgba(255, 193, 7, 0.1);
		color: #856404;
	}

	.type-defer {
		background: rgba(220, 53, 69, 0.1);
		color: var(--danger-color);
	}

	.USDShow,
	.THBShow {
		transition: all 0.3s ease;
	}

	.fade-in {
		animation: fadeIn 0.3s ease-in;
	}

	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.text-right {
		text-align: right !important;
	}

	.text-center {
		text-align: center !important;
	}

	/* Responsive adjustments */
	@media (max-width: 768px) {

		.form-section,
		.data-section {
			padding: 1rem;
		}

		.form-header,
		.data-header {
			margin: -1rem -1rem 1.5rem -1rem;
			padding: 1rem;
		}

		.table-form td:first-child {
			width: auto;
		}
	}
</style>

<div class="purchase-container">
	<div class="row g-0">
		<div class="col-xl-3 col-lg-4">
			<div class="form-section">
				<div class="form-header">
					<h4>
						<i class="fas fa-plus-circle"></i>
						เพิ่มรายการซื้อ
					</h4>
				</div>

				<form name="form_addspot" class="mb-3" onsubmit="fn.app.purchase.spot.add();return false;">
					<input type="hidden" name="pending" value="true">
					<table class="table table-form">
						<tbody>
							<tr>
								<td><label>Supplier</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "supplier_id",
										"type" => "comboboxdb",
										"source" => array(
											"table" => "bs_suppliers",
											"name" => "name",
											"value" => "id",
											"where" => "status = 1"
										)
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>Currency</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "currency",
										"type" => "combobox",
										"source" => array(
											"USD",
											"THB"
										)
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>ประเภทเงิน</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "type",
										"type" => "combobox",
										"source" => array(
											array("physical", "Physical"),
											array("stock", "Stock"),
											array("trade", "Trade"),
											array("defer", "Defer"),
											array("physical-adjust", "Physical Adjust")
										)
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>กิโลซื้อ</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "amount",
										"class" => "text-right",
										"placeholder" => "0.0000"
									));
									?>
								</td>
							</tr>
							<tr class="THBShow">
								<td><label>THB Value</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "THBValue",
										"class" => "text-right",
										"placeholder" => "0.0000"
									));
									?>
								</td>
							</tr>
							<tr class="USDShow">
								<td><label>SPOT Rate</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "rate_spot",
										"caption" => "Spot",
										"class" => "text-right",
										"placeholder" => "Spot Rate",
										"value" => number_format($rate_spot, 4)
									));
									?>
								</td>
							</tr>
							<tr class="USDShow">
								<td><label>PM/DC</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "rate_pmdc",
										"caption" => "Pm/Dc",
										"class" => "text-right pr-4",
										"placeholder" => "premium/discount",
										"value" => "0.0000"

									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>วันที่ซื้อ</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"type" => "date",
										"name" => "date",
										"caption" => "Date",
										"placeholder" => "Purchase Date",
										"value" => date("Y-m-d")
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>Method</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "method",
										"type" => "combobox",
										"caption" => "Method",
										"source" => array(
											"Call To Buy",
											"Deal ID",
											"Via Message"
										)
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>Note</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "noted",
										"type" => "combobox",
										"caption" => "noted",
										"source" => array(
											"Normal",
											"Close-Adjust",
											"Open-Adjust"
										)
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>Reference</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "ref",
										"caption" => "Reference",
										"placeholder" => "Reference Number"
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>Supplier ADJ</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "adj_supplier",
										"type" => "comboboxdb",
										"source" => array(
											"table" => "bs_suppliers",
											"name" => "name",
											"value" => "id",
											"where" => "status = 1"
										)
									));
									?>
								</td>
							</tr>
							<tr>
								<td><label>Product</label></td>
								<td>
									<?php
									$ui_form->EchoItem(array(
										"name" => "product_id",
										"type" => "comboboxdb",
										"source" => array(
											"table" => "bs_products",
											"name" => "name",
											"value" => "id",
											"where" => "id != 9"
										)
									));
									?>
								</td>
							</tr>
						</tbody>
					</table>

					<button class="submit-btn" type="submit">
						<i class="fas fa-check" style="margin-right: 8px;"></i>
						ทำรายการ
					</button>
				</form>
			</div>
		</div>

		<div class="col-xl-9 col-lg-8">
			<div class="data-section">
				<div class="data-header">
					<h4>
						<i class="fas fa-table"></i>
						Pending
					</h4>
				</div>

				<div class="table-responsive">
					<table id="tblPending" class="table table-striped table-hover" width="100%">
						<thead class="bg-dark">
							<tr>
								<th class="text-center text-white">Confirmed</th>
								<th class="text-center text-white">Purchase Date</th>
								<th class="text-center text-white">Type</th>
								<th class="text-center text-white">Note</th>
								<th class="text-center text-white">Supplier</th>
								<th class="text-center text-white">Amount</th>
								<th class="text-center text-white">Spot</th>
								<th class="text-center text-white">PM/DC</th>
								<th class="text-center text-white">User</th>
								<th class="text-center text-white">Ref</th>
								<th class="text-center text-white">Currency</th>
								<th class="text-center text-white">Product</th>
								<th class="text-center text-white">Action</th>
							</tr>
						</thead>
						<tbody>
							<!-- DataTable content will be loaded here -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		// Force style แก้ปัญหา select dropdown ขนาดเล็ก
		function fixSelectHeight() {
			$('form[name="form_addspot"] select, form[name="form_addspot"] input, form[name="form_addspot"] .form-control').each(function() {
				$(this).css({
					'height': '58px',
					'line-height': '25px',
					'padding': '20px 15px',
					'font-size': '1.0rem',
					'vertical-align': 'middle',
					'display': 'block',
					'box-sizing': 'border-box'
				});
			});

			// เฉพาะ select
			$('form[name="form_addspot"] select').css({
				'padding-right': '50px',
				'background-position': 'right 15px center'
			});
		}

		// รันทันทีและหลังจาก DOM เปลี่ยน
		fixSelectHeight();

		// รันซ้ำทุก 500ms เพื่อให้แน่ใจ
		setInterval(fixSelectHeight, 500);
	});
</script>