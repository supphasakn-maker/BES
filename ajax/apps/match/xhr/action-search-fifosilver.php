<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	$balance = -19;
	if($_POST['type']=="daily"){
	?>
	<div class="col-xl-12 mb-12">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<thead>
				<tr>
					<th class="text-center" rowspan="2">Monthly</th>
					<th class="text-center" colspan="3">Sales</th>
					<th class="text-center" colspan="4">Purchase</th>
					
					<th class="text-center" colspan="2">Summary</th>
					
				</tr>
				<tr>
					<th class="text-center">Total Order</th>
					<th class="text-center">Amount</th>
					<th class="text-center">THB Revenue</th>
					
					<th class="text-center">Total Purchase</th>
					<th class="text-center">Amount</th>
					<th class="text-center">USD Value</th>
					<th class="text-center">USD Value(No PMDC)</th>
					
					<th class="text-center">Margin</th>
					<th class="text-center">Balance</th>
				</tr>
			</thead>
			<body>
			<?php
			
			$sql = "SELECT date FROM ((SELECT DATE(date) AS date FROM bs_orders) 
			UNION (SELECT date AS date FROM bs_purchase_spot)) AS tmp 
			WHERE YEAR(date) > 2021 ";
			if($_POST['month'] != ""){
				$time_filter = strtotime($_POST['month']);
				$sql .= " AND date >= '".date("Y-m-d",$time_filter)."'";
				
		
				$sql_order = "SELECT SUM(amount) FROM bs_orders WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND bs_orders.parent IS NULL AND status > -1";
				$rst = $dbc->Query($sql_order);
				$item = $dbc->Fetch($rst);
				$balance -= $item[0];
		
				
				$sql_purchase = "SELECT SUM(amount) FROM bs_purchase_spot WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') AND parent IS NULL AND rate_spot > 0";
				$rst = $dbc->Query($sql_purchase);
				$item = $dbc->Fetch($rst);
				$balance += $item[0];
			}
			$sql .= "GROUP BY(date);";
			echo '<tr>';
				echo '<td class="text-center">Start Balance</td>';
				echo '<td colspan=6">';
				echo '<td class="text-center" ></td>';
				echo '<td class="text-center" >'.$balance.'</td>';
			echo '</tr>';
			$rst = $dbc->Query($sql);
			while($record = $dbc->Fetch($rst)){
				$order = array();
				$purchase = array();
				$margin = 0;
				$sql = "SELECT COUNT(id),SUM(amount),SUM(total) FROM bs_orders WHERE DATE(date) = '".$record['date']."' AND bs_orders.parent IS NULL AND status > -1";
				$rst_order = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_order)){
					array_push($order,array(
						$item[0],$item[1],$item[2]
					));
					$margin -= $item[1];
				}
				
				$sql = "SELECT 
					COUNT(id),
					SUM(amount),
					SUM(amount*32.1507*(rate_spot+rate_pmdc)),
					SUM(amount*32.1507*(rate_spot))
					
				FROM bs_purchase_spot WHERE DATE(date) = '".$record['date']."'
				AND (bs_purchase_spot.type LIKE 'physical'
				OR bs_purchase_spot.type LIKE 'stock'
				) AND parent IS NULL AND rate_spot > 0";
				$rst_purchase = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_purchase)){
					array_push($purchase,array(
						$item[0],$item[1],$item[2],$item[3]
					));
					$margin += $item[1];
				}
				
				$balance += $margin;
				
				
				$total_row = 1;
				if(count($order)>$total_row)$total_row=count($order);
				if(count($purchase)>$total_row)$total_row=count($purchase);
				
				for($i=0;$i<$total_row;$i++){
					echo '<tr>';
					if($i==0){
						$title = $record['date'];
						echo '<td class="text-center" rowspan="'.$total_row.'">'.$title.'</td>';
					}
					if(isset($order[$i])){
						echo '<td class="text-center">'.number_format($order[$i][0],0).'</td>';
						echo '<td class="text-right pr-2">'.number_format($order[$i][1],4).'</td>';
						echo '<td class="text-right pr-2">'.number_format($order[$i][2],2).'</td>';
					}else{
						echo '<td colspan="4">';
					}
					
					if(isset($purchase[$i])){
						echo '<td class="text-center">'.number_format($purchase[$i][0],0).'</td>';
						echo '<td class="text-right pr-2">'.number_format($purchase[$i][1],4).'</td>';
						echo '<td class="text-right pr-2">'.number_format($purchase[$i][2],2).'</td>';
						echo '<td class="text-right pr-2">'.number_format($purchase[$i][3],2).'</td>';
					
					}else{
						echo '<td colspan="4">';
					}
					
					if($i==0){
						echo '<td class="text-right pr-2" rowspan="'.$total_row.'">'.number_format($margin,4).'</td>';
						echo '<td class="text-right pr-2" rowspan="'.$total_row.'">'.number_format($balance,4).'</td>';
					}
					echo '</tr>';
				}
			}
			?>
			</body>
		</table>
	</div>
	<?php
	}else if($_POST['type']=="monthly"){
	?>
	<div class="col-xl-12 mb-12">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<thead>
				<tr>
					<th class="text-center" rowspan="2">Monthly</th>
					<th class="text-center" colspan="3">Sales</th>
					<th class="text-center" colspan="4">Purchase</th>
					
					<th class="text-center" colspan="2">Summary</th>
					
				</tr>
				<tr>
					<th class="text-center">Total Order</th>
					<th class="text-center">Amount</th>
					<th class="text-center">THB Revenue</th>
					
					<th class="text-center">Total Purchase</th>
					<th class="text-center">Amount</th>
					<th class="text-center">USD Value</th>
					<th class="text-center">USD Value (No PMDC)</th>
					
					<th class="text-center">Margin</th>
					<th class="text-center">Balance</th>
				</tr>
			</thead>
			<body>
			<?php
			
			
			
			$sql = "SELECT YEAR(date),MONTH(date)
			FROM ((SELECT DATE(date) AS date FROM bs_orders) 
			UNION (SELECT date AS date FROM bs_purchase_spot)) AS tmp 
			WHERE YEAR(date) > 2021 
			
			";
			if($_POST['month'] != ""){
				$time_filter = strtotime($_POST['month']);
				$sql .= " AND date >= '".date("Y-m-d",$time_filter)."'";
		
				$sql_order = "SELECT SUM(amount) FROM bs_orders WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND bs_orders.parent IS NULL AND status > -1";
				$rst = $dbc->Query($sql_order);
				$item = $dbc->Fetch($rst);
				$balance -= $item[0];
		
				
				$sql_purchase = "SELECT SUM(amount) FROM bs_purchase_spot WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') AND parent IS NULL AND rate_spot > 0";
				$rst = $dbc->Query($sql_purchase);
				$item = $dbc->Fetch($rst);
				$balance += $item[0];
			}
			$sql .= "GROUP BY YEAR(date),MONTH(date);";
			echo '<tr>';
				echo '<td class="text-center">Start Balance</td>';
				echo '<td colspan=6">';
				echo '<td class="text-center" ></td>';
				echo '<td class="text-center" >'.$balance.'</td>';
			echo '</tr>';
			$rst = $dbc->Query($sql);
			while($record = $dbc->Fetch($rst)){
				$order = array();
				$purchase = array();
				$margin = 0;
				$sql = "SELECT COUNT(id),SUM(amount),SUM(total) FROM bs_orders WHERE YEAR(date) = '".$record[0]."' AND MONTH(date) = '".$record[1]."' AND bs_orders.parent IS NULL AND status > -1";
				$rst_order = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_order)){
					array_push($order,array(
						$item[0],$item[1],$item[2]
					));
					$margin -= $item[1];
				}
				
				$sql = "SELECT 
					COUNT(id),
					SUM(amount),
					SUM(amount*32.1507*(rate_spot+rate_pmdc))  ,
					SUM(amount*32.1507*(rate_spot)) 
				FROM bs_purchase_spot WHERE YEAR(date) = '".$record[0]."' AND MONTH(date) = '".$record[1]."'
				AND (bs_purchase_spot.type LIKE 'physical'
				OR bs_purchase_spot.type LIKE 'stock'
				) AND parent IS NULL AND rate_spot > 0";
				$rst_purchase = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_purchase)){
					array_push($purchase,array(
						$item[0],$item[1],$item[2],$item[3]
					));
					$margin += $item[1];
				}
				
				$balance += $margin;
				
				
				$total_row = 1;
				if(count($order)>$total_row)$total_row=count($order);
				if(count($purchase)>$total_row)$total_row=count($purchase);
				
				for($i=0;$i<$total_row;$i++){
					echo '<tr>';
					if($i==0){
						$title = $record[1]."/".$record[0];
						echo '<td class="text-center" rowspan="'.$total_row.'">'.$title.'</td>';
					}
					if(isset($order[$i])){
						echo '<td class="text-center">'.number_format($order[$i][0],0).'</td>';
						echo '<td class="text-right pr-2">'.number_format($order[$i][1],4).'</td>';
						echo '<td class="text-right pr-2">'.number_format($order[$i][2],2).'</td>';
					}else{
						echo '<td colspan="4">';
					}
					
					if(isset($purchase[$i])){
						echo '<td class="text-center">'.number_format($purchase[$i][0],0).'</td>';
						echo '<td class="text-right pr-2">'.number_format($purchase[$i][1],4).'</td>';
						echo '<td class="text-right pr-2">'.number_format($purchase[$i][2],2).'</td>';
						echo '<td class="text-right pr-2">'.number_format($purchase[$i][3],2).'</td>';
					
					}else{
						echo '<td colspan="4">';
					}
					
					if($i==0){
						echo '<td class="text-right pr-2" rowspan="'.$total_row.'">'.number_format($margin,4).'</td>';
						echo '<td class="text-right pr-2" rowspan="'.$total_row.'">'.number_format($balance,4).'</td>';
					}
					echo '</tr>';
				}
			}
			?>
			</body>
		</table>
	</div>
	<?php
	}else{
	?>
	<div class="col-xl-12 mb-12">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<thead>
				<tr>
					<th class="text-center" rowspan="2">Date</th>
					<th class="text-center" colspan="4">Sales</th>
					<th class="text-center" colspan="4">Purchase</th>
					<th class="text-center" colspan="2">Margin</th>
				</tr>
				<tr>
					<th class="text-center">Order ID</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Price</th>
					<th class="text-center">Total</th>
					
					<th class="text-center">Amount</th>
					<th class="text-center">Spot</th>
					<th class="text-center">Rate</th>
					<th class="text-center">Total</th>
					
					
					<th class="text-center">Profit</th>
					<th class="text-center">Balance</th>
				</tr>
			</thead>
			<body>
			<?php
			$sql = "SELECT date FROM ((SELECT DATE(date) AS date FROM bs_orders) 
			UNION (SELECT date AS date FROM bs_purchase_spot)) AS tmp 
			WHERE YEAR(date) > 2021 ";
			if($_POST['month'] != ""){
				$time_filter = strtotime($_POST['month']);
				$sql .= " AND date > '".date("Y-m-d",$time_filter)."'";
				
		
				$sql_order = "SELECT SUM(amount) FROM bs_orders WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND bs_orders.parent IS NULL AND status > -1";
				$rst = $dbc->Query($sql_order);
				$item = $dbc->Fetch($rst);
				$balance -= $item[0];
		
				
				$sql_purchase = "SELECT SUM(amount) FROM bs_purchase_spot WHERE YEAR(date) > 2021 AND date < '".date("Y-m-d",$time_filter)."' AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') AND parent IS NULL AND rate_spot > 0";
				$rst = $dbc->Query($sql_purchase);
				$item = $dbc->Fetch($rst);
				$balance += $item[0];
				
				
				
			}
			$sql .= "GROUP BY(date);";
			echo '<tr>';
				echo '<td class="text-center">Start Balance</td>';
				echo '<td colspan="8">';
				echo '<td class="text-center" ></td>';
				echo '<td class="text-center" >'.$balance.'</td>';
			echo '</tr>';
			$rst = $dbc->Query($sql);
			while($record = $dbc->Fetch($rst)){
				$order = array();
				$purchase = array();
				$margin = 0;
				$sql = "SELECT * FROM bs_orders WHERE DATE(date) = '".$record['date']."' AND bs_orders.parent IS NULL AND status > -1";
				$rst_order = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_order)){
					array_push($order,array(
						$item['code'],$item['amount'],$item['price'],$item['total']
					));
					$margin -= $item['amount'];
				}
				
				$sql = "SELECT * FROM bs_purchase_spot WHERE date = '".$record['date']."' 
				AND (bs_purchase_spot.type LIKE 'physical'
				OR bs_purchase_spot.type LIKE 'stock'
				)AND rate_spot > 0";
				$rst_purchase = $dbc->Query($sql);
				while($item = $dbc->Fetch($rst_purchase)){
					if(!$dbc->HasRecord("bs_purchase_spot","parent=".$item['id'])){
						$total =($item['rate_spot']+$item['rate_pmdc'])*$item['amount']*32.1507;
						array_push($purchase,array(
							$item['amount'],
							$item['rate_spot'],
							$item['rate_pmdc'],
							number_format($total,2)
						));
						$margin += $item['amount'];
					}
				}
				
				$balance += $margin;
				
				
				$total_row = 1;
				if(count($order)>$total_row)$total_row=count($order);
				if(count($purchase)>$total_row)$total_row=count($purchase);
				
				for($i=0;$i<$total_row;$i++){
					echo '<tr>';
					if($i==0){
						echo '<td class="text-center" rowspan="'.$total_row.'">'.$record['date'].'</td>';
					}
					if(isset($order[$i])){
						echo '<td class="text-center">'.$order[$i][0].'</td>';
						echo '<td class="text-center">'.$order[$i][1].'</td>';
						echo '<td class="text-center">'.$order[$i][2].'</td>';
						echo '<td class="text-center">'.$order[$i][3].'</td>';
					}else{
						echo '<td colspan="4">';
					}
					
					if(isset($purchase[$i])){
						echo '<td class="text-center">'.$purchase[$i][0].'</td>';
						echo '<td class="text-center">'.$purchase[$i][1].'</td>';
						echo '<td class="text-center">'.$purchase[$i][2].'</td>';
						echo '<td class="text-center">'.$purchase[$i][3].'</td>';
					}else{
						echo '<td colspan="4">';
					}
					
					if($i==0){
						echo '<td class="text-center" rowspan="'.$total_row.'">'.$margin.'</td>';
						echo '<td class="text-center" rowspan="'.$total_row.'">'.$balance.'</td>';
					}
					echo '</tr>';
				}
			}
			?>
			</body>
		</table>
	</div>	
	
	<?php
	}

	$dbc->Close();
?>
