<div class="card">
	<div class="card-body">
		
		<form name="customer">
			<select name="customer_select" class="form-control select2">
			<?php
			$sql = "SELECT * FROM bs_customers";
			$rst = $dbc->Query($sql);
			while($customer = $dbc->Fetch($rst)){
				echo '<option value="'.$customer['id'].'">'.$customer['name'].'</option>';
			}
			?>
			</select>
			<div id="info_memo"></div>
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>ชื่อลูกค้า</label></td>
						<td><input name="name" type="text" class="form-control" value="-" readonly></td>
						<td rowspan=4><label>comment</label></td>
						<td rowspan=4><textarea name="omment" class="form-control" rows="6" readonly></textarea></td>
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
						<td><label>หมายเหตุ</label></td>
						<td><input name="remark" type="text" class="form-control" value="-" readonly></td>
					</tr>
				</tbody>
			</table>
			<button onclick="fn.app.customer.customer.dialog_edit($('form[name=customer] select[name=customer_select]').val())" class="btn btn-primary" type="button">แก้ไข</button>	
		</form>
	</div>
</div>