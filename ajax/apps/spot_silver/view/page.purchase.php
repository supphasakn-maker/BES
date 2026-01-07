<?php
	global $ui_form,$os;
	
	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");
	$rate_pmdc_purchase = $os->load_variable("rate_pmdc_purchase");
?>
<div class="row">
	<div class="col-3">
		<form name="form_addspot" class="mb-3" onsubmit="fn.app.purchase.spot.add();return false;">
			<table class="table table-bordered table-form">
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
									"value" => "id"
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
									"Physical",
									"Stock",
									"Trade",
									"Defer"
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
								"class" => "text-right pr-4",
								"placeholder" => "0.0000"
							));
						?>
						</td>
					</tr>
					<tr class="THBShow">
						<td><label>THBValue</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "THBValue",
								"class" => "text-right pr-4",
								"placeholder" => "0.0000"
							));
						?>
						</td>
					</tr>
					<tr class="USDShow">
						<td><label>SPOT</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "rate_spot",
								"caption" => "Spot",
								"class" => "text-right pr-4",
								"placeholder" => "Spot Name",
								"value" => number_format($rate_spot,4)
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
								"value" => "0.0000",
								"value" => number_format($rate_pmdc_purchase,4)
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
								"class" => "pl-3",
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
						<td><label>Reference</label></td>
						<td class="pl-2">
						<?php
							$ui_form->EchoItem(array(
								"name" => "ref",
								"caption" => "Reference",
								"placeholder" => "Reference"
							));
						?>
						</td>
					</tr>
				</tbody>
			</table>
			<button class="btn btn-primary" type="submit">ทำรายการ</button>
		</form>
	</div>
	<div class="col-9">
		<table id="tblPurchase" class="table table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center">Confirmed</th>
					<th class="text-center">Purchase Date</th>
					<th class="text-center">Type</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Spot</th>
					<th class="text-center">Pm/Dc</th>
					<th class="text-center">User</th>
					<th class="text-center">Ref</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

