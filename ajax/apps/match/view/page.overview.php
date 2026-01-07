<?php
	global $dbc;
?>
<div class="mb-2">
	<button class="btn btn-primary">Export Excel</button>
	<button class="btn btn-primary" onclick="windows.print()">Print</button>
</div>
<div class="row gutters-sm">
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
			$sql = "SELECT date 
			FROM (
				(SELECT DATE(date) AS date FROM bs_orders)
				UNION (SELECT date AS date FROM bs_purchase_spot)
				UNION (SELECT date AS date FROM bs_purchase_usd)
			) AS tmp   WHERE YEAR(date) > 2021
			
			GROUP BY(date) 
			ORDER BY(date) ASC
			;";
			$rst = $dbc->Query($sql);
			$balance_amount = -19;
			$balance_usd = -4710.36;
			while($record = $dbc->Fetch($rst)){
				$order = array(0,0,0);
				$purchase = array(0,0,0);
				$purchase_usd = array(0,0,0);
				$margin_amount = 0;
				$margin_usd = 0;
				$sql = "SELECT * FROM bs_orders WHERE DATE(date) = '".$record['date']."' AND bs_orders.parent IS NULL AND status > -1";
				$rst_order = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_order)){
					$order[0] += 1;
					$order[1] += $item['amount'];
					$order[2] += $item['total'];
					$margin_amount -= $item['amount'];
				}
				
				$sql = "SELECT * FROM bs_purchase_spot WHERE date = '".$record['date']."'
					AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') 
					AND rate_spot > 0";
					
				$rst_purchase = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_purchase)){
					if(!$dbc->HasRecord("bs_purchase_spot","parent=".$item['id'])){
						$total =($item['rate_spot']+$item['rate_pmdc'])*$item['amount']*32.1507;
						$purchase[0] += 1;
						$purchase[1] += $item['amount'];
						$margin_amount += $item['amount'];
						if($item['currency']!="THB"){
							$purchase[2] += $total;
							$margin_usd -= $total;
						}
					}
				}
				
				
				
				$sql = "SELECT * FROM bs_purchase_usd WHERE date = '".$record['date']."' AND parent IS NULL";
				$rst_purchase = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_purchase)){
					//if(!$dbc->HasRecord("bs_purchase_usd","parent=".$item['id'])){
						
							$purchase_usd[0] += 1;
							$purchase_usd[1] += $item['amount'];
							$margin_usd += $item['amount'];
						
					//}
				}
				
				$balance_amount += $margin_amount;
				$balance_usd += $margin_usd;
				
				
					echo '<tr>';
					
						echo '<td class="text-center">'.$record['date'].'</td>';
					
					
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
						
						echo '<td class="text-right pr-2">'.number_format($balance_amount,4).'</td>';
						
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
</div>