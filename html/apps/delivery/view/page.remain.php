<div class="mb-2">
	<button class="btn btn-outline-dark " onclick="window.history.back()">Back</button>
	<div>
		<table id="tblRemain" class=" mt-2 table table-bordered">
			<thead>
				<tr>
					<th class="text-center">วันที่ผลิต</th>
					<th class="text-center">รอบการผลิต</th>
					<th class="text-center">จำนวนรายการผลิต</th>
					<th class="text-center">จำนวนคงค้าง</th>
					<th class="text-center">น้ำหนักคงค้าง</th>
				</tr>
			</thead>
			<tbody>
				<?php
				global $dbc;
				$data = array();
				// $sql = "SELECT * FROM bs_productions";
				// $rst = $dbc->Query($sql);
				// while($production = $dbc->Fetch($rst)){
				// 	$sql = "SELECT COUNT(bs_packing_items.id),SUM(bs_packing_items.weight_actual)
				// 		FROM bs_packing_items LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
				// 		WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.production_id = ".$production['id']."
				// 		AND bs_packing_items.status >-1
				// 	";

				// 	$rst_count = $dbc->Query($sql);
				// 	$line = $dbc->Fetch($rst_count);
				// 	$item = $dbc->GetRecord("bs_packing_items","COUNT(id),SUM(weight_actual)","production_id=".$production['id']);
				// 	if($line[0] > 0){
				// 		$onclick = 'fn.dialog.open(\'apps/delivery/view/dialog.delivery.remain.php\',\'#dialog_delivery_remain\',{id:'.$production['id'].'})';
				// 		echo '<tr>';
				// 			echo '<td class="text-center">'.$production['created'].'</td>';
				// 			echo '<td class="text-center">'.$production['round'].'</td>';
				// 			echo '<td class="text-center">'.$item[0].'</td>';
				// 			echo '<td class="text-center"><a href="javascript:;" onclick="'.$onclick.'">'.$line[0].'</a></td>';
				// 			echo '<td class="text-center">'.$line[1].'</td>';
				// 		echo '</tr>';
				// 	}




				// }


				?>
			</tbody>
		</table>