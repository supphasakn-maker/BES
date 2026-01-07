
<?php
	global $dbc;
	$start_date = "2022-05-05";
	$today = strtotime("2022-05-04");
	$totaldate = floor((time()-$today)/86400)+7;
	
	
	
	$aDate = array();
	$PID = array(1,3,2);
	$aBalance = array(1204.3810,206.6716,53);
	$aTotal = array();
	$aProduct = array();
	
	
	
		
	for($i=0;$i<count($PID);$i++){
		$product = $dbc->GetRecord("bs_products","*","id=".$PID[$i]);
		array_push($aProduct,$product);
		$line_production = $dbc->GetRecord("bs_productions","SUM(weight_out_packing)","submited BETWEEN '".$start_date."' AND '".date("Y-m-d",$today)."' AND product_id = ".$PID[$i]);
		$line_order = $dbc->GetRecord("bs_orders","SUM(amount)","delivery_date BETWEEN '".$start_date."' AND '".date("Y-m-d",$today-864000)."' AND status > 0 AND product_id = ".$PID[$i]);
		$aBalance[$i]  += $line_production[0]-$line_order[0];
	}
	
	
	$aaData_BF = array(array(),array(),array());
	$aaData_In = array(array(),array(),array());
	$aaData_Out = array(array(),array(),array());
	
	$aaaDataDeposit = array();
	$sql = "SELECT id,name FROM bs_stock_adjuest_types WHERE type = 1 ORDER BY sort_id";
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		array_push($aaaDataDeposit,array(
			array("id" => $item['id'],"name" => $item['name']),array(),array(),array()
		)); //Begin
		
	}
	
	$aaData_CF = array(array(),array(),array());
	
	$aaaDataMemo = array();
	$sql = "SELECT * FROM bs_stock_adjuest_types WHERE type = 2 ORDER BY sort_id";
	$rst = $dbc->Query($sql);
	while($item = $dbc->Fetch($rst)){
		array_push($aaaDataMemo,array(
			array("id" => $item['id'],"name" => $item['name']),array(),array(),array()
		)); //Begin
	}
	
	$aaData_MemoSum = array(array(),array(),array());
	$aaData_Total = array(array(),array(),array());

	
	
	
	for($i=0;$i<=$totaldate;$i++){
		$iDate = $today+$i*86400;
		array_push($aDate,$iDate);
		
		$totalToday = 0;
		for($j=0;$j<count($PID);$j++){
			array_push($aaData_BF[$j],$aBalance[$j]);
			
			$line = $dbc->GetRecord("bs_productions","SUM(weight_out_packing)","submited LIKE '".date("Y-m-d",$iDate)."' AND product_id = ".$PID[$j]);
			array_push($aaData_In[$j],$line[0]); $aBalance[$j] += $line[0];
			
			$line = $dbc->GetRecord("bs_orders","SUM(amount)","delivery_date LIKE '".date("Y-m-d",$iDate)."' AND status > 0 AND product_id = ".$PID[$j]);
			array_push($aaData_Out[$j],$line[0]); $aBalance[$j] -= $line[0];
			$totalToday += $line[0];
			for($k=0;$k<count($aaaDataDeposit);$k++){
				$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = ".$PID[$j]." AND type_id = ".$aaaDataDeposit[$k][0]['id']);
				array_push($aaaDataDeposit[$k][$j+1],$line[0]);
				
				$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = ".$PID[$j]." AND type_id = ".$aaaDataDeposit[$k][0]['id']);
				$aBalance[$j] += $line[0];
			}
			array_push($aaData_CF[$j],$aBalance[$j]);
			
			$buffer = 0;
			for($k=0;$k<count($aaaDataMemo);$k++){
				$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = ".$PID[$j]." AND type_id = ".$aaaDataMemo[$k][0]['id']);
				array_push($aaaDataMemo[$k][$j+1],$line[0]);
				$buffer += $line[0];
			}
			
			array_push($aaData_MemoSum[$j],$buffer);
			array_push($aaData_Total[$j],$aBalance[$j]-$buffer);
			
			
		}
		
		array_push($aTotal,$totalToday);
		
	}
	
	
	
?>
	
<a href="javascript:;" class="" onclick="$('.hidebyclick').toggle()">Toggle Detail</a>
		
	<div class="card-body bg-dark overflow-auto">
		<table class="table table-stock table-bordered table-sm text-white ">
			<tbody>
				<tr>
					<th align="center" style="min-width:150px"><input type="hidden" name="today" value="2020-11-25"></td>
					<?php
						foreach($aDate as $date){
							$class = "";
							if(date("l", $date)=="Sunday")$class="bg-danger";
							if(date("l", $date)=="Saturday")$class="bg-danger";
							echo '<td width="10%" class="'.$class.'" colspan="3" align="center">';
								echo date("l d/m/Y",$date);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-white font-weight-bold">
					<td align="right">สินค้า</td>
					<?php
						
						for($i=0;$i<count($aDate);$i++){
							foreach($aProduct as $product){
								echo '<td align="center">';
									echo $product['name'];
								echo '</td>';
							}
						}
					?>
				</tr>
				<tr class="text-white font-weight-bold">
					<td align="right">ยอดยกมา</td>
					<?php
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaData_BF[$j][$i],4);
								echo '</td>';
							}
						}
					?>
				</tr>
				<tr class="text-success">
					<td align="right">เข้า</td>
					<?php
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaData_In[$j][$i],4);
								echo '</td>';
							}
						}
					?>
					
				</tr>
				<?php
				for($k=0;$k<count($aaaDataDeposit);$k++){
					echo '<tr>';
						echo '<th class="text-right">'.$aaaDataDeposit[$k][0]['name'].'</th>';
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaaDataDeposit[$k][$j+1][$i],4);
								echo '</td>';
							}
							
						}
					echo '</tr>';
				}
				?>
				<tr class="text-danger">
					<td align="right">ออก</td>
					<?php
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaData_Out[$j][$i],4);
								echo '</td>';
							}
						}
					?>
				</tr>
				<tr class="bg-warning text-dark font-weight-bold">
					<td align="right">ยอดส่งรวม</td>
					<?php
						for($i=0;$i<count($aTotal);$i++){
							echo '<td colspan="3" align="center">';
								echo number_format($aTotal[$i],4);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-white font-weight-bold">
					<td align="right">ยอดคงเหลือยกไป</td>
					<?php
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaData_CF[$j][$i],4);
								echo '</td>';
							}
						}
					?>
				</tr>
				<?php
				for($k=0;$k<count($aaaDataMemo);$k++){
					echo '<tr class="text-warning hidebyclick">';
						echo '<th class="text-right".>'.$aaaDataMemo[$k][0]['name'].'</th>';
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaaDataMemo[$k][$j+1][$i],4);
								echo '</td>';
							}
						}
					echo '</tr>';
				}
				?>
				<tr class="text-white bg-primary">
					<td align="right">รวม</td>
					<?php
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaData_MemoSum[$j][$i],4);
								echo '</td>';
							}
						}
					?>
				</tr>
				<tr class="text-white font-weight-bold">
					<td align="right" class="pointer" title="Available Stock">ยอดคงเหลือใช้จริง</td>
					<?php
						for($i=0;$i<count($aDate);$i++){
							for($j=0;$j<count($PID);$j++){
								echo '<td align="right">';
									echo number_format($aaData_Total[$j][$i],4);
								echo '</td>';
							}
						}
					?>
				</tr>
			</tbody>
		</table>
	</div>

<style>
.markdown-result table.table-stock td, .markdown-result table th, .table-bordered td, .table-bordered th {border: 1px solid #3f5771;}.

</style>