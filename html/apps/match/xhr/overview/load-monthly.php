<div class="col-xl-12 mb-12">
	<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
		<thead>
			<tr>
				<th class="text-center table-dark" colspan="13">Date</th>
			</tr>
			<tr>
				<th class="text-center" rowspan="2">Date</th>
				<th class="text-center" colspan="3">Sales</th>
				<th class="text-center" colspan="3">Purchase</th>
				<th class="text-center" colspan="2">USD Purchase</th>
				<th class="text-center" colspan="4">Margin</th>
			</tr>
			<tr>
				<th class="text-center">Total Order</th>
				<th class="text-center">Amount (KG)</th>
				<th class="text-center">Total Value (THB)</th>
				
				<th class="text-center">Total Purchase</th>
				<th class="text-center">Amount</th>
				<th class="text-center">USD Value</th>
				
				<th class="text-center">Purchase</th>
				<th class="text-center">Amount</th>

				<th class="text-center">Amount Margin</th>
				<th class="text-center">Amount Balance</th>
				<th class="text-center">USD Margin</th>
				<th class="text-center">USD Balance</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
		$balance_amount = -19;
		$balance_usd = -4710.36;
		
		$sql = "SELECT YEAR(date),MONTH(date) 
		FROM (
			(SELECT DATE(date) AS date FROM bs_orders)
			UNION (SELECT date AS date FROM bs_purchase_spot)
			UNION (SELECT date AS date FROM bs_purchase_usd)
		) AS tmp   WHERE YEAR(date) > 2021";
		if($_POST['month'] != ""){
			$time_filter = strtotime($_POST['month']);
			$sql .= " AND date >= '".date("Y-m-d",$time_filter)."'";
			
			/*
			$sql_usd = "SELECT SUM(amount) FROM bs_purchase_usd WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND bs_purchase_usd.parent IS NULL AND status > -1";
			$rst = $dbc->Query($sql_usd);
			$item = $dbc->Fetch($rst);
			$balance += $item[0];
	
			$sql_spot = "SELECT SUM(amount*32.1507*(rate_spot+rate_pmdc)) FROM bs_purchase_spot WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') AND parent IS NULL AND rate_spot > 0";
			$rst = $dbc->Query($sql_spot);
			$item = $dbc->Fetch($rst);
			$balance -= $item[0];
			*/
		}
		$sql .= " GROUP BY YEAR(date),MONTH(date);";
		
		$rst = $dbc->Query($sql);
		while($record = $dbc->Fetch($rst)){
			$order = array(0,0,0);
			$purchase = array(0,0,0);
			$purchase_usd = array(0,0,0);
			$margin_amount = 0;
			$margin_usd = 0;
			$sql = "SELECT 
				COUNT(id) AS counter,
				SUM(amount) AS amount,
				SUM(total) AS total 
			FROM bs_orders
			WHERE YEAR(date) = '".$record[0]."' AND MONTH(date) = '".$record[1]."' 
			AND ".$order_condition;
			$rst_order = $dbc->Query($sql);
			while($item = $dbc->Fetch($rst_order)){
				$order[0] += $item['counter'];
				$order[1] += $item['amount'];
				$order[2] += $item['total'];
				$margin_amount -= $item['amount'];
			}
			
			$sql = "SELECT 
				COUNT(bs_purchase_spot.id) AS counter,
				SUM((rate_spot+rate_spot)*amount*32.1507)  AS total,
				SUM(amount) AS amount
				FROM bs_purchase_spot WHERE YEAR(date) = '".$record[0]."' AND MONTH(date) = '".$record[1]."' 
				AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') 
				AND rate_spot > 0 AND status > -1";
				
			$rst_purchase = $dbc->Query($sql);
			while($item = $dbc->Fetch($rst_purchase)){
				//if(!$dbc->HasRecord("bs_purchase_spot","parent=".$item['id'])){
					
					$purchase[0] += $item['counter'];
					$purchase[1] += $item['amount'];
					$margin_amount += $item['amount'];
					//if($item['currency']!="THB"){
						$purchase[2] += $item['total'];
						$margin_usd -= $item['total'];
					//}
				//}
			}
			
			
			
			$sql = "SELECT 
				COUNT(bs_purchase_usd.id) AS counter,
				SUM(amount) AS amount 
			FROM bs_purchase_usd WHERE  YEAR(date) = '".$record[0]."' AND MONTH(date) = '".$record[1]."'  AND parent IS NULL";
			$rst_purchase = $dbc->Query($sql);
			while($item = $dbc->Fetch($rst_purchase)){
				//if(!$dbc->HasRecord("bs_purchase_usd","parent=".$item['id'])){
					
						$purchase_usd[0] += $item['counter'];
						$purchase_usd[1] += $item['amount'];
						$margin_usd += $item['amount'];
					
				//}
			}
			
			$balance_amount += $margin_amount;
			$balance_usd += $margin_usd;
			
			
				echo '<tr>';
				
					$title = $record[1]."/".$record[0];
					echo '<td class="text-center">'.$title.'</td>';
				
					echo '<td class="text-center">'.number_format($order[0],0).'</td>';
					echo '<td class="text-right pr-2">'.number_format($order[1],4).'</td>';
					echo '<td class="text-right pr-2">'.number_format($order[2],2).'</td>';
				
				
			
					echo '<td class="text-center">'.number_format($purchase[0],0).'</td>';
					echo '<td class="text-right pr-2">'.number_format($purchase[1],4).'</td>';
					echo '<td class="text-right pr-2">'.number_format($purchase[2],2).'</td>';
					
					echo '<td class="text-center">'.number_format($purchase_usd[0],0).'</td>';
					echo '<td class="text-right pr-2">'.number_format($purchase_usd[1],2).'</td>';
					
					$margin_class = ($margin_amount > 0)?" text-success":" text-danger";
					echo '<td class="text-right pr-2'.$margin_class.'">'.number_format($margin_amount,4).'</td>';
					$margin_class = ($balance_amount > 0)?" text-success":" text-danger";
					echo '<td class="text-right pr-2'.$margin_class.'">'.number_format($balance_amount,4).'</td>';
					
					$margin_class = ($margin_usd > 0)?" text-success":" text-danger";
					echo '<td class="text-right pr-2'.$margin_class.'">'.number_format($margin_usd,2).'</td>';
					echo '<td class="text-right pr-2">'.number_format($balance_usd,2).'</td>';
				
				
				
				echo '</tr>';
			
			
			
		
		}
		?>
			
		</tbody>
		<thead>
			
			<tr>
				<th class="text-center" rowspan="2">Date</th>
				<th class="text-center" colspan="3">Sales</th>
				<th class="text-center" colspan="3">Purchase</th>
				<th class="text-center" colspan="2">USD Purchase</th>
				<th class="text-center" colspan="4">Margin</th>
			</tr>
			<tr>
				<th class="text-center">Total Order</th>
				<th class="text-center">Amount (KG)</th>
				<th class="text-center">Total Value (THB)</th>
				
				<th class="text-center">Total Purchase</th>
				<th class="text-center">Amount</th>
				<th class="text-center">USD Value</th>
				
				<th class="text-center">Purchase</th>
				<th class="text-center">Amount</th>

				<th class="text-center">Amount Margin</th>
				<th class="text-center">Amount Balance</th>
				<th class="text-center">USD Margin</th>
				<th class="text-center">USD Balance</th>
			</tr>
		</thead>
	</table>
</div>	