<table class="table table-sm table-bordered table-striped">
	<thead>
		<tr>
			<td class="text-center">วันที่</td>
			<th class="text-center">เปิด TR</th>
			<th class="text-center">ชำระ TR</th>
			<th class="text-center">สกุลเงิน</th>
			<th class="text-center">ดอกเบี้ย</th>
			<td class="text-center">วันที่ตั๋ว</td>
			<td class="text-center">หมายเลขตั๋ว</td>
		</tr>
	</thead>
	<tbody>
	<?php
		$aSum = array(0,0,0);
		$sql = "SELECT 
			bs_transfer_payments.date AS date,
			bs_transfer_payments.currency AS currency,
			bs_transfer_payments.principle AS principle,
			bs_transfer_payments.paid AS paid,
			bs_transfer_payments.interest AS interest,
			bs_transfer_payments.rate_counter AS rate_counter,
			bs_transfers.date AS tr_date,
			bs_transfers.code AS tr_code
		
		FROM bs_transfer_payments 
		LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id
		WHERE DATE_FORMAT(bs_transfer_payments.date,'%Y-%m') = '".$_POST['month']."' 
		AND bs_transfers.bank = '".$_POST['bank']."'
		AND bs_transfer_payments.currency = '".$_POST['currency']."'
		";
		$rst = $dbc->Query($sql);
		while($payment = $dbc->Fetch($rst)){
			if($payment['currency']=="USD"){
				//$thb_interest = $payment['rate_counter']*$payment['interest'];
				$thb_interest = $payment['interest'];
			}else{
				$thb_interest = $payment['interest'];
			}
			

			echo '<tr>';
				echo '<td class="text-center">'.$payment['date'].'</td>';
				echo '<td class="text-right"></td>';
				echo '<td class="text-right">'.number_format($payment['paid'],2).'</td>';
				echo '<td class="text-center">'.$payment['currency'].'</td>';
				echo '<td class="text-right">'.number_format($thb_interest,2).'</td>';
				echo '<td class="text-center">'.$payment['tr_date'].'</td>';
				echo '<td class="text-center">'.$payment['tr_code'].'</td>';
				
			echo '</tr>';
			$aSum[0] += 1;
			$aSum[1] += $payment['paid'];
			$aSum[2] += $thb_interest;
			
		}

	?>
	</tbody>
	<thead class="bg-primary">
		<?php
		$aSumTR = array(0,0);
		$sql = "SELECT code , value_thb_net ,date  FROM bs_transfers WHERE DATE_FORMAT(bs_transfers.date,'%Y-%m') = '".$_POST['month']."' 
		AND bs_transfers.bank = '".$_POST['bank']."' ";
		$rst = $dbc->Query($sql);
		while($payment = $dbc->Fetch($rst))	{
			echo '<tr>';
				echo '<td class="text-center text-white font-weight-bold">'.$payment['date'].'</td>';
				echo '<td class="text-right text-white font-weight-bold">'.number_format($payment['value_thb_net'],2).'</td>';
				echo '<td class="text-right"></td>';
				echo '<td class="text-center"></td>';
				echo '<td class="text-right"></td>';
				echo '<td class="text-center"></td>';
				echo '<td class="text-center text-white font-weight-bold">'.$payment['code'].'</td>';
				
			echo '</tr>';
			$aSumTR[0] += 1;
			$aSumTR[1] += $payment['value_thb_net'];
		}
		?>
	</thead>
	<tfoot>
		<tr>
			<th class="text-center">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th></th>
			<th class="text-right"><?php echo number_format($aSum[1],2)?></th>
			<th></th>
			<th class="text-right"><?php echo number_format($aSum[2],2)?></th>
			<th></th>
			<th></th>
		</tr>
		<tr>
		<th class="text-center">เปิดตั๋วทั้งหมด <?php echo $aSumTR[0]; ?>  รายการ</th>
		<th class="text-right"><?php echo number_format($aSumTR[1],2)?></th>
		<th colspan="6"></th>
		</tr>
	</tfoot>
</table>

