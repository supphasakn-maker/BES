<?php
global $ui_form, $os;
$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");
?>
<div class="row">
	<div class="col-3">
		<form name="form_adddefer" class="mb-3" onsubmit="fn.app.defer_spot.defer.add();return false;">

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
									"value" => "id",
									"where" => "status = 1"
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
								"placeholder" => "0.00"
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>PRICE</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"name" => "price",
								"caption" => "PRICE",
								"class" => "text-right pr-4",
								"placeholder" => "0.00"
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>SPOT</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"name" => "rate_spot",
								"caption" => "Spot",
								"class" => "text-right pr-4",
								"placeholder" => "Spot Name",
								"value" => $rate_spot
							));
							?>

						</td>
					</tr>
					<tr>
						<td><label>PM/DC</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"name" => "rate_pmdc",
								"caption" => "Pm/Dc",
								"class" => "text-right pr-4",
								"placeholder" => "premium/discount",
								"value" => "0.00"
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>วันที่</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "date",
								"name" => "value_date",
								"caption" => "Date",
								"class" => "pl-3",
								"placeholder" => "Purchase Date",
								"value" => date("Y-m-d")
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
		<table id="tblDefer" class="table table-striped table-bordered table-hover table-middle" width="100%">
			<thead class="bg-dark">
				<tr>
					<th class="text-center text-white font-weight">DATE/TIME</th>
					<th class="text-center text-white font-weight">SUPPLIER</th>
					<th class="text-center text-white font-weight">SPOT</th>
					<th class="text-center text-white font-weight">PM/DC</th>
					<th class="text-center text-white font-weight">AMOUNT</th>
					<th class="text-center text-white font-weight">PRICE</th>
					<th class="text-center text-white font-weight">VALUE DATE</th>
					<th class="text-center text-white font-weight">REF</th>
					<th class="text-center text-white font-weight">USER</th>
					<th class="text-center text-white font-weight">ACTION</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>