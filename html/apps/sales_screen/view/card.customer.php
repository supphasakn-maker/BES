<div class="card">
	<div class="card-body">

		<form name="customer">
			<select name="customer_select" class="form-control select2">
				<?php
				$sql = "SELECT * FROM bs_customers ORDER BY name";
				$rst = $dbc->Query($sql);
				while ($customer = $dbc->Fetch($rst)) {
					echo '<option value="' . $customer['id'] . '">' . $customer['name'] . '</option>';
				}
				?>
			</select>
			<div id="info_memo"></div>
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>รหัสลูกค้า</label></td>
						<td><input name="id" type="text" class="form-control" value="-" readonly></td>
						<td><label>เบอร์ SILVER NOW</label></td>
						<td><input name="silvernow_no" type="number" class="form-control" value="-" readonly></td>
					</tr>
					<tr>
						<td><label>ชื่อลูกค้า</label></td>
						<td><input name="name" type="text" class="form-control" value="-" readonly></td>
						<td rowspan=4><label>ที่อยู่จัดส่ง</label></td>
						<td rowspan=4><textarea name="shipping_address" class="form-control" rows="6" readonly></textarea></td>
					</tr>
					<tr>
						<td><label>เบอร์โทรศัพท์</label></td>
						<td><input name="phone" type="text" class="form-control" value="-" readonly></td>
					</tr>
					<tr>
						<td><label>แฟร์ก</label></td>
						<td><input name="fax" type="text" class="form-control" value="-" readonly></td>
					</tr>
					<tr>
						<td><label>ชื่อผู้ติดต่อ</label></td>
						<td><input name="contact" type="text" class="form-control" value="-" readonly></td>
					</tr>
					<tr>
						<td><label>อีเมลล์</label></td>
						<td><input name="email" type="text" class="form-control" value="-" readonly></td>
						<td><label>ธนาคาร</label></td>
						<td><input name="default_bank" type="text" class="form-control" value="-" readonly></td>
					</tr>
					<tr>
						<td><label>ผู้ขาย</label></td>
						<td><input name="default_sales" type="text" class="form-control" value="-" readonly></td>
						<td><label>ข้อมูลได้รับการยกเว้น VAT</label></td>
						<td><input name="remark" type="text" class="form-control" value="-" readonly></td>
					</tr>
					<tr>
						<td><label>การชำระเงิน</label></td>
						<td><input name="default_payment" type="text" class="form-control" value="-" readonly></td>
						<td><label>ภาษีมูลค่าเพิ่ม</label></td>
						<td>

							<input name="default_vat_type" type="text" class="form-control" value="-" readonly>

						</td>
					</tr>
					<tr>
						<td><label>ข้อมูลแบ่งแพ็ค</label></td>

						<td colspan="3">

							<input name="default_pack" type="text" class="form-control" value="-" readonly>

						</td>
					</tr>
					<tr>
						<td><label>ข้อมูลลูกค้า</label></td>

						<td colspan="3">

							<input name="new_cus" type="text" class="form-control" value="-" readonly>

						</td>
					</tr>
				</tbody>
			</table>
			<button onclick="fn.app.customer.customer.dialog_edit($('form[name=customer] select[name=customer_select]').val())" class="btn btn-primary" type="button">แก้ไข</button>
		</form>
	</div>
</div>