<div class="card h-100">
	<div class="card-header border-0">
		<h6>ทำรายการสั่งซื้อ</h6>
	</div>
	<div class="card-body">
		<form name="form_addcontract">
			<input type="hidden" name="date" value="<?php echo date("Y-m-d");?>">
			<input type="hidden" name="select_usd" value="">
			<input type="hidden" name="select_amount" value="">
			<input type="hidden" name="select_bank_date" value="">
			<input type="hidden" name="select_premium_date" value="">
			<input type="hidden" name="select_contact_no" value="">
			<input type="hidden" name="select_premium" value="">
			<input type="hidden" name="select_purchase" value="">
			<input type="hidden" name="select_thbpremium" value="">
			
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
								"caption" => "ธนาคาร",
								"default" => array(
									"value" => "none",
									"name" => "ไม่ระบุ"
								)
								
							));
						?>
						</td>
						<td><label>วันที่ โอน</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "date",
								"name" => "transfer_date"
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>ชำระเงิน</label></td>
						<td colspan=3>
						<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "method",
								"source" => array(
									"ค่าสินค้า","Deposit","จ่าย Non-Fixed"
								)
							));
						?>
						</td>
					</tr>
					
					<tr>
						<td><label>เลือก</label></td>
						<td colspan="3">
							<div>
								<select name="supplier_id" type="text" class="form-control" placeholder="Slect Source">
								<option value="">โปรดเลือก Supplier</option>
								<?php
									$sql = "SELECT * FROM bs_suppliers";
									$rst = $dbc->Query($sql);
									while($line = $dbc->Fetch($rst)){
										echo '<option value="'.$line['id'].'">'.$line['name'].'</option>';
									}
								?>
								</select>
							</div>
							<div id="select_purchase" class="p-2" style="max-height:200px;overflow-y: scroll;">
							
							</div>
						</td>
					</tr>
					<tr>
						<td><label>Fixed Rate</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "fixed_value",
								"value" => 0,
								"readonly" => true
							));
						?>
						</td>
						<td><label>Non Fixed</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "nonfixed_value",
								"value" => 0,
								"readonly" => true
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>Deposit</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "deposit",
								"value" => 0
							));
						?>
						</td>
						
						<td><label>Counter Rate</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "rate_counter",
								"value" => 0
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>Net Goods Value</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "net_good_value",
								"value" => 0,
								"readonly" => true
							));
						?>
						</td>
						<td><label>Total Transfer</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "total_transfer",
								"value" => 0,
								"readonly" => true
							));
						?>
						</td>
					</tr>
				</tbody>
			</table>
			<button class="btn btn-primary" type="button" onclick="fn.app.forward_contract.contract.add()">ทำรายการ</button>
			<button class="btn btn-warning" type="button" onclick="fn.app.forward_contract.contract.calculate();">Calculate</button>
			
			
		</form>
	</div>
</div>