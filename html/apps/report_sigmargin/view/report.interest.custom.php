<table class="table table-sm table-bordered table-striped">
	<thead>
		<tr>
			<td class="text-center">วันที่</td>
			<th class="text-center">เงินต้น</th>
			<th class="text-center">สกุลเงิน</th>
			<th class="text-center">ดอกเบี้ย</th>
			<td class="text-center">วันที่ตั๋ว</td>
			<td class="text-center">หมายเลขชขตั๋ว</td>
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
		WHERE bs_transfer_payments.date BETWEEN '".$_POST['date_from']."' AND '".$_POST['date_to']."'
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
	<tfoot>
		<tr>
			<th class="text-center">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th class="text-right"><?php echo number_format($aSum[1],2)?></th>
			<th></th>
			<th class="text-right"><?php echo number_format($aSum[2],2)?></th>
		</tr>
	</tfoot>
</table>

