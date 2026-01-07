<div class="card h-100">
	<div class="card-header border-0">
		<h6>ทำรายการสั่งซื้อ</h6>
	</div>
	<div class="card-body">
		<form name="form_addimport">
			<input type="hidden" name="date" value="<?php echo date("Y-m-d");?>">
			<input type="hidden" name="select_spot" value="">
			<input type="hidden" name="select_amount" value="">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Source</label></td>
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
						<td><label>Date</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "date",
								"name" => "date"
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>ชำระเงิน </label></td>
						<td class="p-2">
							<input name="payment" value="a" type="radio"> TEst
							<input name="payment" value="b" type="radio"> TEst
							<input name="payment" value="c" type="radio"> TEst
						</td>
					</tr>
					<tr>
						<td><label>เลือก</label></td>
						<td colspan=3><div id="select_usd" class="p-2"></div></td>
					</tr>
					<tr>
						<td><label>Fixed Rate</label></td>
						<td><input name="" class="form-control"></td>
						<td><label>Non Fixed Rate</label></td>
						<td><input name="" class="form-control"></td>
					</tr>
					<tr>
						<td><label>Total Transfer</label></td>
						<td><input name="" class="form-control"></td>
						<td><label>Counter Rate</label></td>
						<td><input name="" class="form-control"></td>
					</tr>
					<tr>
						<td><label>Net Good Value</label></td>
						<td><input name="" class="form-control"></td>
						<td><label>Deposit</label></td>
						<td><input name="" class="form-control"></td>
					</tr>
				</tbody>
			</table>
			<button class="btn btn-primary" type="button" onclick="fn.app.trust_receipt.tr.add()">ทำรายการ</button>
		</form>
	</div>
</div>