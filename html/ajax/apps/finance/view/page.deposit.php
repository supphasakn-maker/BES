<?php
	global $dbc;
	$today = time();
?>
<div class="btn-area btn-group mb-2">


<table id="tblDeposit" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center">ลูกค้า</th>
			<th class="text-center">ยอดเงินมัดจำ</th>
			<th class="text-center">ยอดมัดจำคงเหลือ</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$sql = "SELECT * FROM bs_payment_deposits WHERE status=1";
		$rst = $dbc->Query($sql);
		while($deposit = $dbc->Fetch($rst)){
			$customer = $dbc->GetRecord("bs_customers","*","id=".$deposit['customer_id']);
			$deposit_used = $dbc->GetRecord("bs_payment_deposit_use","SUM(amount)","deposit_id=".$deposit['id']);
			echo '<tr>';
				echo '<td>'.$customer['name'].'</td>';
				echo '<td class="text-right">'.$deposit['amount'].'</td>';
				echo '<td class="text-right">'.($deposit['amount']-$deposit_used[0]).'</td>';
			echo '</tr>';
		}
	
	
	?>
	</tbody>
</table>
