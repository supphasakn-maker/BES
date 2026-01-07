<?php
	global $ui_form,$os;
	
	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");
?>
<div class="row">
	<div class="col-3">
		<form name="form_addusd" class="mb-3" onsubmit="fn.app.purchase.usd.add();return false;">
			
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Bank</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "comboboxdatabank",
								"source" => "db_bank",
								"name" => "bank",
								"caption" => "ธนาคารที่ซื้อ",
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>ประเภท</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "type",
								"type" => "combobox",
								"source" => array(
									"Physical",
									"Stock",
									"Trade"
								)
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>จำนวน USD</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "amount",
								"class" => "text-right pr-4",
								"placeholder" => "0.00"
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>Exchange Rate</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "rate_exchange",
								"caption" => "Rate Exchange",
								"class" => "text-right pr-4",
								"placeholder" => "Exchange Rate",
								"value" => $rate_exchange
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
						<td><label>Remark</label></td>
						<td class="pl-2">
						<?php
							$ui_form->EchoItem(array(
								"type" => "textarea",
								"name" => "comment",
								"caption" => "Comment",
								"placeholder" => "Comment"
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
					<th class="text-center">Confirm</th>
					<th class="text-center">Purchase Date</th>
					<th class="text-center">Type</th>
					<th class="text-center">Bank</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Exchange</th>
					<th class="text-center">Remark</th>
					<th class="text-center">User</th>
					<th class="text-center">Splited</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

