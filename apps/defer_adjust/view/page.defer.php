<?php
global $ui_form, $os;
?>
<div class="row">
	<div class="col-3">
		<form name="form_adddefer" class="mb-3" onsubmit="fn.app.defer_adjust.defer.add();return false;">

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
						<td><label>VALUE ADJUST</label></td>
						<td class="pl-2">
							<?php
							$ui_form->EchoItem(array(
								"name" => "value_adjust_type",
								"caption" => "VALUE ADJUST",
								"placeholder" => "0.0000"
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>PRODUCT</label></td>
						<td class="pl-2">
							<?php
							$ui_form->EchoItem(array(
								"name" => "product_id",
								"type" => "comboboxdb",
								"source" => array(
									"table" => "bs_products",
									"name" => "name",
									"value" => "id",
									"where" => "status = 1"
								)
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
					<th class="text-center text-white font-weight">DATE</th>
					<th class="text-center text-white font-weight">SUPPLIER</th>
					<th class="text-center text-white font-weight">VALUE ADJUST TYPE</th>
					<th class="text-center text-white font-weight">PRODUCT NAME</th>
					<th class="text-center text-white font-weight">ACTION</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>