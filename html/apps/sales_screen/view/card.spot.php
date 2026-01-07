<?php
	global $ui_form,$os;
	
	$today = time();

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);


	$sql1 = "SELECT * FROM bs_price  ORDER BY id DESC LIMIT 1";
	$rss = $dbc->Query($sql1);
	$lastsilver = $dbc->Fetch($rss);



?>

<div class="card mb-2">
	<div class="card-body">
		<form name="rate" onsubmit="fn.app.sales_screen.add_spot();return false;">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td colspan="4" class="text-center h4" style="height: 50px; overflow:hidden;"><label>ราคาเม็ดเงิน</label></td>
					</tr>
					<tr>
						<td><label>Spot</label></td>
						<td colspan="3"><input name="rate_spot" type="number"  step="0.01" class="form-control text-right" value="<?php echo number_format($lastsilver['rate_spot'],2);?>" onchange="fn.app.sales_screen.recalcuate()"></td>
						
					</tr>
					<tr>
						<td><label>อัตราแลกเปลี่ยน</label></td>
						<td colspan="2"><input name="rate_exchange" step="0.01" type="number" class="form-control text-right" value="<?php echo number_format($lastsilver['rate_exchange'],2);?>" onchange="fn.app.sales_screen.recalcuate()"></td>
						<td class="text-left">THB/USD</td>
					</tr>
					<tr>
						<td><label>Pm/Dc</label></td>
						<td colspan="3"><input readonly name="discount" type="number" class="form-control text-right" value="<?php echo $rate_pmdc;?>" onchange="fn.app.sales_screen.recalcuate()"></td>
					</tr>
					<tr>
						<td><label>Pm/Dc Recycle</label></td>
						<td colspan="3"><input readonly name="discount_recycle" type="number" class="form-control text-right" value="<?php echo $rate_pmdc_recycle;?>" onchange="fn.app.sales_screen.recalcuate()"></td>
						
					</tr>
					<tr>
						<td><label>ราคาเม็ดเงิน LBMA 1</label></td>
						<td colspan="3"> 
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">฿</span>
								</div>
								<input type="text" readonly name="price1" class="form-control">
								<div class="input-group-append">
									<span class="input-group-text">+20</span>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td><label>ราคาเม็ดเงิน LBMA 2</label></td>
						<td colspan="3"><input type="text" readonly name="price2" class="form-control text-right" onchange="fn.app.sales_screen.recalcuate()"></td>
					</tr>
					<tr>
						<td><label>ราคาเม็ดเงิน LBMA 3</label></td>
						<td colspan="3"><input type="text" readonly name="price3" class="form-control text-right" onchange="fn.app.sales_screen.recalcuate()"></td>
					</tr>
					<tr>
						<td><label>ราคาเม็ดเงิน RECYCLE 1</label></td>
						<td colspan="3"> 
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">฿</span>
								</div>
								<input type="text" readonly name="price4" class="form-control" onchange="fn.app.sales_screen.recalcuate()">
								<div class="input-group-append">
									<input type="text" readonly name="rate_recycle1" value="+<?php echo $rate_recycle1;?>" class="form-control text-right" onchange="fn.app.sales_screen.recalcuate()">
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td><label>ราคาเม็ดเงิน RECYCLE 2</label></td>
						<td colspan="3"> 
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">฿</span>
								</div>
								<input type="text" readonly name="price5" class="form-control" onchange="fn.app.sales_screen.recalcuate()">
								<div class="input-group-append">
									<input type="text" readonly name="rate_recycle2" value="+<?php echo $rate_recycle2;?>" class="form-control text-right" onchange="fn.app.sales_screen.recalcuate()">
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td><label>ราคาเม็ดเงิน LBMA กำหนดเอง</label></td>
						<td colspan="3">
							<input type="text" name="margin" class="form-control text-right" value="60" onchange="fn.app.sales_screen.recalcuate()">
							<input type="text" name="price" class="form-control text-right" style="border-top:1px solid #555" value="<?php echo $rate_spot*$rate_exchange;?>">
						
						</td>
						
					</tr>
					<tr>
						<td colspan="4"><button class="btn btn-danger">บันทึก</button></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>