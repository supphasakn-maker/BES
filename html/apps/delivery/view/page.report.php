<div class="mb-2">
	<button class="btn btn-outline-dark " onclick="window.history.back()">Back</button>
	<div>
		<table class="table mt-2 table-bordered">
			<thead>
				<tr>
					<th class="text-center">รายการ</th>
					<!-- <th class="text-center">ชนิด</th> -->
					<th class="text-center">จำนวน</th>
					<th class="text-center">น้ำหนักรวม</th>
					<th class="text-center">หมายเหตุ</th>
				</tr>
				<thead>
				<tbody>
					<?php
					global $dbc;
					// $data = array();
					// $sql = "SELECT  
					// bs_packing_items.id AS id,
					// bs_packing_items.pack_name AS pack_name, 
					// bs_packing_items.pack_type AS pack_type,
					// COUNT(bs_packing_items.id) AS total_item,
					// SUM(bs_packing_items.weight_actual) AS weight_actual,
					// SUM(bs_packing_items.weight_expected) AS weight_expected
					// FROM bs_packing_items 
					// LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
					// LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
					// WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL 
					// GROUP BY bs_packing_items.pack_name 
					// ORDER BY bs_packing_items.pack_name  DESC";
					// $rst = $dbc->Query($sql);
					// while ($item = $dbc->Fetch($rst)) {

					// 	echo '<tr>';
					// 	echo '<td class="text-left">' . $item['pack_name'] . '</td>';
					// 	echo '<td class="text-center">' . $item['pack_type'] . '</td>';
					// 	echo '<td class="text-center">' . $item['total_item'] . '</td>';
					// 	echo '<td class="text-center">' . number_format($item['weight_expected'], 4) . '</td>';
					// 	echo '</tr>';
					// }

					$sql = "SELECT pack_name FROM bs_packing_items 
					LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
					LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
					 WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL 
					GROUP BY bs_packing_items.pack_name 
					ORDER BY bs_packing_items.pack_name  DESC";
					$rst = $dbc->Query($sql);
					while ($record = $dbc->Fetch($rst)) {
						$order = array(0, 0, 0, 0);
						$sql = "SELECT  
					COUNT(bs_packing_items.id) AS total_item,
					SUM(bs_packing_items.weight_actual) AS weight_actual,
					SUM(bs_packing_items.weight_expected) AS weight_expected
					FROM bs_packing_items 
					LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
					LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
					WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = '" . $record['pack_name'] . "'";
						$rst_order = $dbc->Query($sql);
						while ($item = $dbc->Fetch($rst_order)) {

							$order[0] += 1;
							$order[1] += $item['total_item'];
							$order[2] += $item['weight_actual'];
							$order[3] += $item['weight_expected'];
						}

						$total_row = 1;
						for ($i = 0; $i < $total_row; $i++) {
							echo '<tr>';
							if ($i == 0) {
								$title = $record['pack_name'];
								echo '<td class="text-left" rowspan="' . $total_row . '">' . $title . '</td>';
								echo '<td class="text-center">' . number_format($order[1], 0) . '</td>';
								echo '<td class="text-center">' . number_format($order[3], 4) . '</td>';
							}
						}
						echo '<td>';
						$sql = "SELECT bs_packing_items.code
					FROM bs_packing_items 
					LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
					LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
					WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = '" . $record['pack_name'] . "'";
						$rst_driver = $dbc->Query($sql);
						while ($driver = $dbc->Fetch($rst_driver)) {
							echo '<span class="badge badge-dark">' . $driver['code'] . '</span>';
						}
						echo '</td>';
					}




					?>
				</tbody>
		</table>