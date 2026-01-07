<?php
	$start_date = "2022-03-30";
	$today = strtotime("2022-03-29");
	$totaldate = floor((time()-$today)/86400)+7;
	$aDate = array();
	
	$PID = array(1,3,2);
	$aBalance = array(558,650,97);
	
	$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'BWD'");
		
	
	for($i=0;$i<count($PID);$i++){
		$line_production = $dbc->GetRecord("bs_productions","SUM(weight_out_packing)","submited BETWEEN '".$start_date."' AND '".date("Y-m-d",$today)." 00:00:00' AND product_id = ".$PID[$i]);
		$line_order = $dbc->GetRecord("bs_orders","SUM(amount)","delivery_date BETWEEN '".$start_date."' AND '".date("Y-m-d",$today)."' AND status > 0 AND product_id = ".$PID[$i]);
		$aBalance[$i]  += $line_production[0]-$line_order[0];
	}
	
	
	$aaData = array();
	for($i=0;$i<count($PID);$i++){
		$aData = array();
		array_push($aData,array()); //Begin
		array_push($aData,array()); //In
		array_push($aData,array()); //Deposit
		array_push($aData,array()); //Out
		array_push($aData,array()); //Total
		array_push($aData,array()); //A/S
		
		array_push($aData,array()); //BWD
		array_push($aData,array()); //BWS
		array_push($aData,array()); //LBMA
		
		array_push($aData,array()); //Total
		array_push($aData,array()); //C/F
		
	}
	
	
	$aDataBegin = array();
	$aDataIn = array();
	$aDataOut = array();
	$aDataDeposit = array();
	$aDataAS = array();
	$aDataClose = array();
	$aDataBWD = array();
	$aDataBWS = array();
	$aDataLBMA = array();
	$aDataBuffer = array();
	
	$aDataBeginLB = array();
	$aDataInLB = array();
	$aDataOutLB = array();
	$aDataDepositLB = array();
	$aDataASLB = array();
	$aDataCloseLB = array();
	$aDataBWDLB = array();
	$aDataBWSLB = array();
	$aDataLBMALB = array();
	$aDataBufferLB = array();
	
	$aDataBeginBar = array();
	$aDataInBar = array();
	$aDataOutBar = array();
	$aDataDepositBar = array();
	$aDataASBar = array();
	$aDataCloseBar = array();
	$aDataBWDBar = array();
	$aDataBWSBar = array();
	$aDataLBMABar = array();
	
	
	for($i=0;$i<=$totaldate;$i++){
		$iDate = $today+$i*86400;
		array_push($aDate,$iDate);
		
		
		
		
		
		
		
		
		$aDataBuffer[$i] = 0;
		$aDataBufferLB[$i] = 0;
		
		array_push($aDataBegin,$balance);
		$line = $dbc->GetRecord("bs_productions","SUM(weight_out_packing)","submited LIKE '".date("Y-m-d",$iDate)."' AND product_id = 1");
		array_push($aDataIn,$line[0]);
		$balance += $line[0];
		$line = $dbc->GetRecord("bs_orders","SUM(amount)","delivery_date LIKE '".date("Y-m-d",$iDate)."' AND status > 0 AND product_id = 1");
		array_push($aDataOut,$line[0]);
		$balance -= $line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'deposit'");
		array_push($aDataDeposit,$line[0]);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'deposit'");
		$balance += $line[0];
		array_push($aDataAS,$balance);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'BWD'");
		array_push($aDataBWD,$line[0]);
		$aDataBuffer[$i] +=$line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'BWD'");
		$balance += $line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'BWS'");
		array_push($aDataBWS,$line[0]);
		$aDataBuffer[$i] +=$line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'BWS'");
		$balance += $line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'LBMA'");
		array_push($aDataLBMA,$line[0]);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 1 AND type = 'LBMA'");
		$balance += $line[0];
		array_push($aDataClose,$balance);
		
		
		
		array_push($aDataBeginLB,$balanceLB);
		$line = $dbc->GetRecord("bs_productions","SUM(weight_out_packing)","submited LIKE '".date("Y-m-d",$iDate)."' AND product_id = 3");
		array_push($aDataInLB,$line[0]);
		$balanceLB += $line[0];
		$line = $dbc->GetRecord("bs_orders","SUM(amount)","delivery_date LIKE '".date("Y-m-d",$iDate)."' AND status > 0 AND product_id = 3");
		array_push($aDataOutLB,$line[0]);
		$balanceLB -= $line[0];
		array_push($aDataASLB,$balanceLB);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'deposit'");
		array_push($aDataDepositLB,$line[0]);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'deposit'");
		$balanceLB += $line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'BWD'");
		array_push($aDataBWDLB,$line[0]);
		$aDataBufferLB[$i] +=$line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'BWD'");
		$balanceLB += $line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'BWS'");
		array_push($aDataBWSLB,$line[0]);
		$aDataBufferLB[$i] +=$line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'LBMA'");
		$balanceLB += $line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 3 AND type = 'LBMA'");
		array_push($aDataLBMALB,$line[0]);
		array_push($aDataCloseLB,$balanceLB);
		
		array_push($aDataBeginBar,$balanceBar);
		$line = $dbc->GetRecord("bs_productions","SUM(weight_out_packing)","submited LIKE '".date("Y-m-d",$iDate)."' AND product_id = 2");
		array_push($aDataInBar,$line[0]);
		$balanceBar += $line[0];
		$line = $dbc->GetRecord("bs_orders","SUM(amount)","delivery_date LIKE '".date("Y-m-d",$iDate)."' AND status > 0 AND product_id = 2");
		array_push($aDataOutBar,$line[0]);
		$balanceBar -= $line[0];
		array_push($aDataASBar,$balanceBar);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 2 AND type = 'deposit'");
		array_push($aDataDepositBar,$line[0]);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date = '".date("Y-m-d",$iDate)."' AND product_id = 2 AND type = 'deposit'");
		$balanceBar += $line[0];
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 2 AND type = 'BWD'");
		array_push($aDataBWDBar,$line[0]);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 2 AND type = 'BWS'");
		array_push($aDataBWSBar,$line[0]);
		$line = $dbc->GetRecord("bs_stock_adjusted","SUM(amount)","date <= '".date("Y-m-d",$iDate)."' AND product_id = 2 AND type = 'LBMA'");
		array_push($aDataLBMABar,$line[0]);
		array_push($aDataCloseBar,$balanceBar);
		
	}
	
	foreach($aDate as $date){
		array_push($aData,array(
		
		));
	}
?>
<div class="card mb-3 ">
<button class="btn btn-outline-dark" onclick="$('.hidebyclick').toggle()">Toggle Detail</button>
		
	<div class="card-body bg-dark overflow-auto">
		<table class="table table-sm table-bordered text-white ">
			<tbody>
				<tr>
					<td width="4%" align="center"><input type="hidden" name="today" value="2020-11-25"></td>
					<?php
						foreach($aDate as $date){
							echo '<td width="10%" colspan="3" align="center">';
								echo date("D-d",$date);
							echo '</td>';
						}
					?>
				</tr>
				<tr>
					<td align="right">B/f</td>
					<?php
						for($i=0;$i<count($aDataBegin);$i++){
							echo '<td align="right">';
								echo number_format($aDataBegin[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataBeginLB[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataBeginBar[$i]);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-success">
					<td align="right">In</td>
					<?php
						for($i=0;$i<count($aDataIn);$i++){
							echo '<td width="3%" align="right">';
								echo number_format($aDataIn[$i],4);
							echo '</td>';
							echo '<td width="3%" align="right">';
								echo number_format($aDataInLB[$i],4);
							echo '</td>';
							echo '<td width="3%" align="right">';
								echo number_format($aDataInBar[$i]);
							echo '</td>';
						}
					?>
					
				</tr>
				<tr class="text-primary">
					<td align="right">ฝาก</td>
					<?php
						for($i=0;$i<count($aDataDeposit);$i++){
							echo '<td width="3%" align="right">';
								echo number_format($aDataDeposit[$i],4);
							echo '</td>';
							echo '<td width="3%" align="right">';
								echo number_format($aDataDepositLB[$i],4);
							echo '</td>';
							echo '<td width="3%" align="right">';
								echo number_format($aDataDepositBar[$i]);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-danger">
					<td align="right">Out</td>
					<?php
						for($i=0;$i<count($aDataOut);$i++){
							echo '<td width="3%" align="right">';
								echo number_format($aDataOut[$i],4);
							echo '</td>';
							echo '<td width="3%" align="right">';
								echo number_format($aDataOutLB[$i],4);
							echo '</td>';
							echo '<td width="3%" align="right">';
								echo number_format($aDataOutBar[$i]);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="bg-warning text-dark font-weight-bold">
					<td align="right">Total</td>
					<?php
						for($i=0;$i<count($aDataOut);$i++){
							echo '<td colspan="3" align="center">';
								echo number_format(($aDataOut[$i]+$aDataOutLB[$i]+$aDataOutBar[$i]),4);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-white font-weight-bold">
					<td align="right">A/S</td>
					<?php
						for($i=0;$i<count($aDataAS);$i++){
							echo '<td align="right">';
								echo number_format($aDataAS[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataASLB[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataASBar[$i]);
							echo '</td>';
						}
					?>
				</tr>
				
				<tr class="text-warning hidebyclick">
					<td align="right">BWD</td>
					<?php
						for($i=0;$i<count($aDataBWD);$i++){
							echo '<td align="right">';
								echo number_format($aDataBWD[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataBWDLB[$i],4);
							echo '</td>';
							echo '<td align="right">';
								//echo number_format($aDataBWDBar[$i]);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-warning hidebyclick">
					<td align="right">BWS</td>
					<?php
						for($i=0;$i<count($aDataBWS);$i++){
							echo '<td align="right">';
								echo number_format($aDataBWS[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataBWSLB[$i],4);
							echo '</td>';
							echo '<td align="right">';
								//echo number_format($aDataBWSBar[$i]);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-warning hidebyclick">
					<td align="right">LBMA</td>
					<?php
						for($i=0;$i<count($aDataLBMA);$i++){
							echo '<td align="right">';
								echo number_format($aDataLBMA[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataLBMALB[$i],4);
							echo '</td>';
							echo '<td align="right">';
								//echo number_format($aDataLBMABar[$i]);
							echo '</td>';
						}
					?>
				</tr>
				<tr class="text-info hidebyclick">
					<td align="right">Total</td>
					
					<?php
					
						for($i=0;$i<count($aDataBuffer);$i++){
							echo '<td align="right">';
								echo number_format($aDataBuffer[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataBufferLB[$i],4);
							echo '</td>';
							echo '<td align="right">';
								//echo number_format($aDataCloseBar[$i]);
							echo '</td>';
						}
						
					?>
				</tr>
				<tr class="text-white font-weight-bold">
					<td align="right">C/f</td>
					<?php
						for($i=0;$i<count($aDataClose);$i++){
							echo '<td align="right">';
								echo number_format($aDataClose[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataCloseLB[$i],4);
							echo '</td>';
							echo '<td align="right">';
								echo number_format($aDataCloseBar[$i]);
							echo '</td>';
						}
					?>
				</tr>
			</tbody>
		</table>
	</div>
</div>