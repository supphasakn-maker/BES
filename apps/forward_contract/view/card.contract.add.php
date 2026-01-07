<div class="card h-100">
	<div class="card-header border-0">
		<h6>ทำรายการสั่งซื้อ</h6>
	</div>
	<div class="card-body">
		<form name="form_addcontract">
			<input type="hidden" name="select_usd_id" value="">
			<input type="hidden" name="select_amount" value="">
			<input type="hidden" name="select_bank_date" value="">
			<input type="hidden" name="select_premium_date" value="">
			<input type="hidden" name="select_contact_no" value="">
			<input type="hidden" name="select_premium" value="">
			<input type="hidden" name="select_purchase" value="">
			<input type="hidden" name="select_thbpremium" value="">
			
			<input type="hidden" name="value_usd_adjusted" value="">
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
								"name" => "date"
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>ชำระเงิน</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "type",
								"source" => array(
									"ค่าสินค้า","Deposit","จ่าย Non-Fixed"
								)
							));
						?>
						</td>
						<td><label>ที่มา</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "source",
								"source" => array(
									array(1,"Defered Spot"),
									array(2,"Import"),
									array(3,"Combine Import"),
									array(4,"Splitted Defered Spot")
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
									$sql = "SELECT * FROM bs_suppliers WHERE status = '1' ";
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
						<td><label>Net Goods Value(USD)</label></td>
						<td colspan="3">
						<?php
							$ui_form->EchoItem(
							array(
								"name" => "value_usd_goods",
								"value" => 0,
								"class" => "text-right",
								"readonly" => true
							));
						?>
						</td>
					</tr>
					<tr>
						<td class="bg-success text-white"><label>Deposit(USD)</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_usd_deposit",
								"class" => "text-right",
								"value" => 0
							));
						?>
						</td>
						<td class="bg-danger text-white"><label>Paid USD</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_usd_paid",
								"class" => "text-right",
								"value" => 0
							));
						?>
						</td>
					</tr>
					<tr>
						<td class="bg-warning text-white"><label>Adjustment</label></td>
						<td id="adjustment" colspan="3">
							<button type="button" onclick="fn.app.forward_contract.contract.append_adjustment()" class="btn btn-xs btn-dark">Append Adjustment</button>
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td class="bg-info text-white"><label>ค่าทำเนียมการใช้ (THB)</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_thb_transaction",
								"class" => "text-right",
								"value" => 0
							));
						?>
						</td>
					</tr>
					<tr>
						<td class="bg-warning"><label>Total USD</label></td>
						<td colspan="3">
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_usd_total",
								"value" => 0,
								"class" => "text-right",
								"readonly" => true
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>Non Fixed</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_usd_nonfixed",
								"value" => 0,
								"class" => "text-right",
								"readonly" => true
							));
						?>
						</td>
						<td><label>Counter Rate</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "rate_counter",
								"class" => "text-right",
								"value" => 0
							));
						?>
						</td>
						
						
					</tr>
					<tr>
						<td><label>Fixed Value</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_usd_fixed",
								"value" => 0,
								"class" => "text-right",
								"readonly" => true
							));
						?>
						</td>
						<td><label>(THB)</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_thb_fixed",
								"value" => 0,
								"class" => "text-right",
								"readonly" => true
							));
						?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td><label>Total Premium (THB)</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_thb_premium",
								"value" => 0,
								"class" => "text-right",
								"readonly" => true
							));
						?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td><label>Total (THB)</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "value_thb_net",
								"value" => 0,
								"class" => "text-right",
								"readonly" => true
							));
						?>
						</td>
					</tr>
				</tbody>
			</table>
			<button class="btn btn-primary" type="button" onclick="fn.app.forward_contract.contract.add()">ทำรายการ</button>
			<button class="btn btn-warning" type="button" onclick="fn.app.forward_contract.contract.calculate();">Calculate</button>
			
			<div class="pt-2">
				<table id="tblSelected" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center">Date</th>
							<th class="text-center">USD Amount</th>
							<th class="text-center">Rate Exchange</th>
							<th class="text-center">THB Value</th>
							<th class="text-center">Start Date/Premium Date</th>
							<th class="text-center">Preminum Date</th>
							<th class="text-center">Contract No.</th>
							<th class="text-center">Preminum</th>
							<th class="text-center">Fx + Premium</th>
							<th class="text-center">THB+Premium</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			
		</form>
	</div>
</div>
<div id="aTransferAdjusted" class="d-none">
<?php
	$data = $os->load_variable("aTransferAdjusted");
	$items = json_decode($data,true);
	
	foreach($items as $item){
		echo '<option>'.$item.'</option>';
	}

?>


</div>