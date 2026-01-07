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
						</td>
						<td><label>Delivery By</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "delivery_by",
								"source" => array(
									"Brink",
									"G4S",
									"รับเอง"
								)
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>เลือก</label></td>
						<td><div id="select_reserve" class="p-2"></div></td>
						<td rowspan=2><label>หมายเหตุ</label></td>
						<td rowspan=2><textarea name="comment" class="form-control"></textarea></td>
					</tr>
					<tr>
						<td><label>กิโลที่จะได้รับ </label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"name" => "amount"
							));
						?>
						</td>
					</tr>
					<tr>
						<td><label>แท่ง/เม็ด </label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "type",
								"source" => array(
									"แท่ง",
									"เม็ด"
								)
							));
						?>
						</td>
						<td><label>วันที่มาส่ง</label></td>
						<td>
						<?php
							$ui_form->EchoItem(array(
								"type" => "date",
								"name" => "delivery_date",
								"value" => date("Y-m-d")
							));
						?>
						</td>
					
					</tr>
					
				</tbody>
				
			</table>
			<button class="btn btn-primary" type="button" onclick="fn.app.import.import.add()">ทำรายการ</button>
		</form>
	</div>
</div>