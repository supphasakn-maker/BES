
<div class="card h-100">
	<div class="card-header border-0">
		<h6>ทำรายการสั่งซื้อ</h6>
	</div>
	<div class="card-body">
		<form name="order">
			<input type="hidden" name="date" value="<?php echo date("Y-m-d");?>">
			<input type="hidden" name="rate_spot" value="<?php echo $rate_spot;?>">
			<input type="hidden" name="rate_exchange" value="<?php echo $rate_exchange;?>">
			<input type="hidden" name="currency" value="THB">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>ลูกค้า</label></td>
						<td colspan="2">
							<select name="customer_id" type="text" class="form-control select2" placeholder="โปรดเลือกลูกค้า">
								<option value="">โปรดเลือกลูกค้า</option>
							<?php
								$sql = "SELECT * FROM bs_customers";
								$rst = $dbc->Query($sql);
								while($line = $dbc->Fetch($rst)){
									echo '<option value="'.$line['id'].'">'.$line['name'].'</option>';
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label>สั่งซื้อจำนวน</label></td>
						<td colspan="2">
							<div class="input-group mb-3">
								<input name="amount" type="text" class="form-control" value="0.00">
								<div class="input-group-append">
								<select name="product_id" type="text" class="form-control">
								<?php
									$sql = "SELECT * FROM bs_products";
									$rst = $dbc->Query($sql);
									while($line = $dbc->Fetch($rst)){
										echo '<option value="'.$line['id'].'">'.$line['name'].'</option>';
									}
								?>
								</select>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td><label>ราคา</label></td>
						<td><input name="price" type="text" class="form-control" value="0.00"></td>
						<td class="text-left">บาท/กิโล</td>
					</tr>
					<tr>
						<td><label>ภาษีมูลค่าเพิ่ม</label></td>
						<td>
							<select name="vat_type" class="form-control">
								<option value="0">VAT 0%</option>
								<option value="2">VAT 7%</option>
							</select>
						
						</td>
						<td class="text-left">%</td>
					</tr>
					<tr>
						<td><label>รวมราคา</label></td>
						<td><input name="net" type="text" class="form-control" readonly></td>
					</tr>
					
					<tr>
						<td><label>ผู้สั่งสินค้า</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "contact"
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>วันที่ส่งของ</label></td>
						<td><input name="delivery_date" type="date" class="form-control" style="width: 175px;"></td>
						<td class="p-0 text-center">
							<div class="custom-control custom-checkbox form-control rounded-0 border-0">
								<input name="delivery_lock" type="checkbox" class="custom-control-input" id="chkLock">
								<label class="custom-control-label" for="chkLock"><i class="far fa-lock"></i></label>
							</div>
						</td>
					</tr>
					<tr>
						<td><label>เวลาส่งของ</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "comboboxdatabank",
								"source" => "db_time",
								"name" => "delivery_time",
								"default" => array(
									"value" => "none",
									"name" => "ไม่ระบุ"
								)
							));
						?>
						</td>
					</tr>
				</tbody>
			</table>
			<button class="btn btn-secondary" type="button" onclick="fn.app.sales_screen.reset()">ยกเลิก</button>
			<button class="btn btn-primary" type="button" onclick="fn.app.sales_screen.add_order()">ทำรายการ</button>
		</form>
	</div>
</div>