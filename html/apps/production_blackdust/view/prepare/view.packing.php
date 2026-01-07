<div class="row">
	<div class="col-8">
		<div class="mb-2">
			<a type="button" onclick="fn.app.production_summarize.prepare.dialog_add_pack(<?php echo $production['id'];?>)" class="btn btn-primary mr-2">เพิ่มถุง</a>
			<a type="button" onclick="fn.app.production_summarize.prepare.calculation()" class="btn btn-warning">Calucate</a>
		
		</div>
		<table id="tblPacking" data-id="<?php echo $production['id'];?>" class="mt-2 table table-bordered">
			<thead>
				<tr>
					<th></th>
					<th class="text-center">หมายเลขถุง</th>
					<th class="text-center">ขนาดถุง</th>
					<th class="text-center">ประเภทถุง</th>
					<th class="text-center">นำหนักตามประเภท</th>
					<th class="text-center">น้ำหนักจริง</th>
					<th class="text-center">สถานะ</th>
				</tr>
				
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>

	<div class="col-4">
		
		<?php
			$sql = "SELECT * FROM bs_stock_prepare WHERE status = 1 ORDER BY delivery_date";
			$rst = $dbc->Query($sql);
			
			if($dbc->Total($rst)>0){
				echo '<div class="alert alert-warning">ไม่พบรายการ</div>';
			}else{
				echo '<div class="alert alert-warning">ไม่พบรายการ</div>';
			}
			//var_dump($item);
		?>
		
		<div id="Output">
		</div>
		
		<select name="stock_prepare" class="form-control">
		<?php
			$sql = "SELECT * FROM bs_stock_prepare WHERE status=1";
			$rst = $dbc->Query($sql);
			while($item = $dbc->Fetch($rst)){
				echo '<option value="'.$item['id'].'">'.$item['prepare_date']." ".$item['info_amount'].'</option>';
			}
		
		?>
		</select>
		<div id="OutputA">
		</div>
		<?php
			$prepare = $dbc->GetRecord("bs_stock_prepare","*","1 ORDER BY created DESC");
		?>
		
	</div>
	
</div>