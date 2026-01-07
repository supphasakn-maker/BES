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
			<th class="text-center">รายการ</th>			
			<th class="text-center">จำนวนรวม</th>
			<th class="text-center">จำนวนรายการ</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$sql = "
			SELECT 
				bs_payments.customer_id AS customer_id,
				bs_payments.date_active AS date_active,
				bs_customers.name AS customer_name,
				bs_payment_types.name AS type,
				SUM(bs_payment_items.amount) AS total,
				COUNT(bs_customers.id) AS count_item

			FROM bs_payment_items
			LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
			LEFT JOIN bs_payment_types ON bs_payment_types.id = bs_payment_items.type_id
			LEFT JOIN bs_customers ON bs_customers.id = bs_payments.customer_id
			WHERE status=1
			GROUP BY bs_payments.customer_id,bs_payment_items.type_id
		";
		$rst = $dbc->Query($sql);
		while($line = $dbc->Fetch($rst)){
			echo '<tr>';
				echo '<td>'.$line['date_active'].'</td>';
				echo '<td>'.$line['customer_name'].'</td>';
				echo '<td>'.$line['type'].'</td>';		
				echo '<td class="text-right">'.number_format($line['total'],2).'</td>';
				echo '<td class="text-right">'.$line['count_item'].'</td>';
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