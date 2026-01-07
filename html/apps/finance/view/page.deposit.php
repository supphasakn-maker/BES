<?php
	global $dbc;
	$today = time();
?>
<div class="btn-area btn-group mb-2">


<table id="tblDeposit" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center">วันที่</th>
			<th class="text-center">ลูกค้า</th>
			<th class="text-center">ยอดเงินมัดจำ</th>
			<th class="text-center">ยอดมัดจำคงเหลือ</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$sql = "SELECT bs_payments.date_active,bs_payment_deposits.customer_id,bs_payment_deposits.id,bs_payment_deposits.amount,bs_payments.ref  FROM bs_payments
		LEFT OUTER JOIN bs_payment_deposits ON bs_payments.id = bs_payment_deposits.payment_id
		where bs_payment_deposits.status = 1 and bs_payments.ref LIKE '%โอนล่วงหน้า%' AND YEAR(date_active) > 2022 ";
		$rst = $dbc->Query($sql);
		while($deposit = $dbc->Fetch($rst)){
			$customer = $dbc->GetRecord("bs_customers","*","id=".$deposit['customer_id']);
			$deposit_used = $dbc->GetRecord("bs_payment_deposit_use","SUM(amount)","deposit_id=".$deposit['id']);
			$use = $deposit['amount']-$deposit_used[0];
			echo '<tr>';
				echo '<td>'.$deposit['date_active'].'</td>';
				echo '<td>'.$customer['name'].'</td>';
				echo '<td class="text-right">'.$deposit['amount'].'</td>';
				echo '<td class="text-right">'.number_format($use,4).'</td>';
			echo '</tr>';
		}
	
	
	?>
	</tbody>
</table>
<script>
	$(function() {  
//Created By: Brij Mohan
//Website: https://techbrij.com
function groupTable($rows, startIndex, total){
if (total === 0){
return;
}
var i , currentIndex = startIndex, count=1, lst=[];
var tds = $rows.find('td:eq('+ currentIndex +')');
var ctrl = $(tds[0]);
lst.push($rows[0]);
for (i=1;i<=tds.length;i++){
if (ctrl.text() ==  $(tds[i]).text()){
count++;
$(tds[i]).addClass('deleted');
lst.push($rows[i]);
}
else{
if (count>1){
ctrl.attr('rowspan',count);
groupTable($(lst),startIndex+1,total-1)
}
count=1;
lst = [];
ctrl=$(tds[i]);
lst.push($rows[i]);
}
}
}
groupTable($('#tblDeposit tr:has(td)'),0,3);
$('#tblDeposit .deleted').remove();
});
</script>