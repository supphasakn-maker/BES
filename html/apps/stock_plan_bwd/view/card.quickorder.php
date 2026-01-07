<div class="card mb-6">
	<div class="card-header border-0">
		<h6>Sales (Quick)</h6>
	</div>
	<div class="card-body">
		<div>
			<form name="form_addquick_order" onsubmit="fn.app.sales_bwd.quick_order.add();return false;">
				<div class="input-group">
                    <input name="customer" type="text" class="form-control" placeholder="ลูกค้า">
                    <select name="platform" class="form-control">
						<option value="Shopee">Shopee</option>
						<option value="Website">Website</option>
                        <option value="Lazada">Lazada</option>
                        <option value="LINE">LINE</option>
                        <option value="Facebook">Facebook</option>
					</select>
					<input type="number" step=".0001" name="amount" class="form-control" placeholder="จำนวน/แท่ง" autocomplete="off">
					<input name="price" class="form-control" placeholder="ราคา" autocomplete="off" >
					<select name="discount" class="form-control">
						<option value="0">ไม่มีส่วนลด</option>
						<option value="1">5%</option>
                        <option value="2">10%</option>
                        <option value="3">15%</option>
                        <option value="4">20%</option>
                        <option value="5">25%</option>
                        <option value="6">30%</option>
					</select>
				</div>
				<div class="input-group">
					<select name="product_id" class="form-control" placeholder="โปรดเลือกสินค้า">
						
						<?php
							$sql = "SELECT * FROM bs_products_bwd";
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								echo '<option value="'.$line['id'].'">'.$line['name'].'</option>';
							}
						?>
					</select>
                    <select name="product_type" class="form-control" placeholder="โปรดเลือกประเภทสินค้า">
						<?php
							$sql = "SELECT * FROM bs_products_type";
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								echo '<option value="'.$line['id'].'">'.$line['name'].'</option>';
							}
						?>
					</select>
                    <select name="discount" class="form-control">
						<option value="0">ไม่มีส่วนลด</option>
						<option value="1">5%</option>
                        <option value="2">10%</option>
                        <option value="3">15%</option>
                        <option value="4">20%</option>
                        <option value="5">25%</option>
                        <option value="6">30%</option>
					</select>
					<input name="remark" type="text" class="form-control" placeholder="remark">
					<div class="input-group-append">
					</div>
				</div>
				<button class="btn btn-block btn-dark" type="submit">Add</button>
					
			</form>
		</div>
		<table id="tblQuickOrder" class="datatable table table-striped table-sm table-bordered nowrap">
			<!-- Filter columns -->
			<thead class="bg-dark">
				<tr>
					<th class="text-center text-white"><?php echo date('Y-m-d');?></th>
					<th class="text-center text-white">PO</th>
					<th class="text-center text-white">CUSTOMER</th>
					<th class="text-center text-white">KGS.</th>
					<th class="text-center text-white">PRICE</th>
					<th class="text-center text-white">PRICE+VATS</th>
					<th class="text-center text-white">STATUS</th>
					<th class="text-center text-white">SP</th>
					<th class="text-center text-white">EX</th>
					<th class="text-center text-white">SALES</th>
					<!-- <th class="text-center text-white">PRODUCT</th> -->
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tbody>
				<tr>
					<th class="text-right" colspan="2">ยอดรวม</th>
					<th class="text-center" xname="tAmount"></th>
					<th class="text-right">รวมทั้งหมด</th>
					<th class="text-center" colspan="6" xname="tValue"></th>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>