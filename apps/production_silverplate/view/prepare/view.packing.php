<div class="row">
	<div class="col-8">
		<div class="mb-2">
			<a type="button" onclick="fn.app.production_silverplate.prepare.dialog_add_pack(<?php echo $production['id']; ?>)" class="btn btn-primary mr-2">เพิ่มถุง</a>
			<a type="button" onclick="fn.app.production_silverplate.prepare.calculation()" class="btn btn-warning">Calculate</a>

		</div>
		<table id="tblPacking" data-id="<?php echo $production['id']; ?>" class="mt-2 table table-bordered">
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

				<?php
				/*
				$sql = "SELECt * FROM bs_packing_items WHERE production_id = ".$production['id'];
				$rst = $dbc->Query($sql);
				while($pack = $dbc->Fetch($rst)){
					
					
					echo '<tr>';
						echo '<td><a onclick="fn.app.production_silverplate.prepare.remove_pack(this);" href="javascript:;" class="btn btn-sm btn-danger">X</a>';
						echo '<input type="hidden" name="pack_id[]" value="'.$pack['id'].'" class="pack_id form-control">';
						echo '<input type="hidden" name="pack_method[]" value="" class="pack_method form-control">';
						echo '<input type="hidden" name="weight_expected[]" value="'.$pack['weight_expected'].'" class="pack_method form-control">';
						echo '</td>';
						echo '<td><input xname="pack_code" name="pack_code[]" value="'.$pack['code'].'" class="form-control"></td>';
						echo '<td>';
							echo '<input xname="pack_name" readonly name="pack_name[]" value="'.$pack['pack_name'].'" class="pack_method form-control">';
						echo '</td>';
						echo '<td>';
							echo '<select name="pack_type[]" class="form-control">';
								echo '<option'.($pack['pack_type']=="ถุงปกติ"?" selected":"").'>ถุงปกติ</option>';
								echo '<option'.($pack['pack_type']=="ถุงกระสอบ"?" selected":"").'>ถุงกระสอบ</option>';
							echo '</select>';
						echo '</td>';
						echo '<td><input xname="weight_actual" name="weight_actual[]" value="'.$pack['weight_actual'].'" class="form-control" placeholder="น้ำหนักที่ชั่งไว้"></td>';
					echo '</tr>';
					
				}
				*/
				?>
			</tbody>
		</table>
	</div>

	<div class="col-4">

		<?php
		$sql = "SELECT * FROM bs_stock_prepare WHERE status = 1 ORDER BY delivery_date";
		$rst = $dbc->Query($sql);

		if ($dbc->Total($rst) > 0) {
			echo '<div class="alert alert-warning">ไม่พบรายการ</div>';
		} else {
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
			while ($item = $dbc->Fetch($rst)) {
				echo '<option value="' . $item['id'] . '">' . $item['prepare_date'] . " " . $item['info_amount'] . '</option>';
			}

			?>
		</select>
		<div id="OutputA">
		</div>
		<?php
		$prepare = $dbc->GetRecord("bs_stock_prepare", "*", "1 ORDER BY created DESC");
		?>

	</div>

</div>