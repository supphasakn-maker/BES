<div class="mb-2">
	<button class="btn btn-outline-dark " onclick="window.history.back()">Back</button>
<div>
<table class="table">
<tbody>
<?php
	global $dbc;
	$data = array();
	$sql = "SELECT * FROM bs_packing_items WHERE delivery_id IS NULL";
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		
		$iterator = -1;
		for($i=0;$i<count($data);$i++){
			if($data[$i]['name']==$item['pack_name']){
				$iterator = $i;
			}
		}
		
		if($iterator>-1){
			$data[$iterator]['total']++;
			$data[$iterator]['weight'] += $item['weight_actual'];
			$data[$iterator]['remark'] .= ",".$item['code'];
			
		}else{
			
			echo '<tr>';
				echo '<td>'.$item['pack_name'].'</td>';
				echo '<td>'.$item['pack_name'].'</td>';
				echo '<td>'.$item['weight_actual'].'</td>';
				echo '<td>'.$item['code'].'</td>';
			echo '</tr>';
			array_push($data,array(
				"name" => $item['pack_name'],
				"total" => 1,
				"weight" => $item['weight_actual'],
				"remark" => $item['code']
			));
		}
		
		
		
	}


?>
</tbody>
</table>