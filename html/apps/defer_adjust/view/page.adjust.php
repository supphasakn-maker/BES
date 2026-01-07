<?php
	global $ui_form,$os;
?>
<div class="row">
	<div class="col-3">
		<form name="form_addadjust" class="mb-3" onsubmit="fn.app.defer_adjust.adjust.add();return false;">
			
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>SUPPLIER</label></td>
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
						<td><label>DATE</label></td>
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
						<td><label>AMOUNT</label></td>
						<td class="pl-2">
						<?php
							$ui_form->EchoItem(array(
								"name" => "amount",
								"caption" => "AMOUNT",
								"placeholder" => "0.0000"
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>USD</label></td>
						<td class="pl-2">
						<?php
							$ui_form->EchoItem(array(
								"name" => "usd",
								"caption" => "USD",
								"placeholder" => "0.0000"
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
		<table id="tblAdjust" class="table table-striped table-bordered table-hover table-middle" width="100%">
			<thead class="bg-dark">
				<tr>
					<th class="text-center text-white font-weight">DATE</th>
					<th class="text-center text-white font-weight">SUPPLIER</th>
					<th class="text-center text-white font-weight">AMOUNT</th>
					<th class="text-center text-white font-weight">USD</th>
					<th class="text-center text-white font-weight">ACTION</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

