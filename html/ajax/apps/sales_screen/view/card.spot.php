<div class="card mb-2">
	<div class="card-body">
		<form name="rate" onsubmit="return false;">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Spot</label></td>
						<td colspan="3"><input name="spot" type="number" class="form-control text-right" value="<?php echo $rate_spot;?>" onchange="fn.app.sales_screen.recalcuate()"></td>
						
					</tr>
					<tr>
						<td><label>อัตราแลกเปลี่ยน</label></td>
						<td colspan="2"><input name="exchange" step="0.01" type="number" class="form-control text-right" value="<?php echo $rate_exchange;?>" onchange="fn.app.sales_screen.recalcuate()"></td>
						<td class="text-left">THB/USD</td>
					</tr>
					<tr>
						<td><label>Pm/Dc</label></td>
						<td colspan="3"><input readonly name="discount" type="number" class="form-control text-right" value="<?php echo $rate_pmdc;?>" onchange="fn.app.sales_screen.recalcuate()"></td>
					</tr>
					<tr>
						<td><label>ราคา 1</label></td>
						<td><input type="text" readonly name="price1" class="form-control text-right" onchange="fn.app.sales_screen.recalcuate()"></td>
					</tr>
					<tr>
						<td><label>ราคา 2</label></td>
						<td><input type="text" readonly name="price2" class="form-control text-right" onchange="fn.app.sales_screen.recalcuate()"></td>
					</tr>
					<tr>
						<td><label>ราคา 3</label></td>
						<td><input type="text" readonly name="price3" class="form-control text-right" onchange="fn.app.sales_screen.recalcuate()"></td>
					</tr>
					<tr>
						<td><label>ราคา กำหนดเอง</label></td>
						<td>
							<input type="number" name="margin" class="form-control text-right" value="20" onchange="fn.app.sales_screen.recalcuate()">
							<input type="number" name="price" class="form-control text-right" style="border-top:1px solid #555" value="<?php echo $rate_spot*$rate_exchange;?>">
						
						</td>
						<td colspan="2"><button class="btn btn-danger">บันทึก</button></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>