<?php
	$dd = date("Y-m-d");
	$aa = date('Y-m-d',strtotime("-1 days"));
	$sql = "SELECT * FROM bs_announce_silver WHERE date = '".$dd."' ORDER BY no DESC LIMIT 1";
	$rst = $dbc->Query($sql);
	// $silver = $dbc->Fetch($rst);
	if ($rst->num_rows > 0)
	while($silver = $rst->fetch_assoc()) {
	
	$allvat = $silver['sell']*7/100 ;
	$all = $silver['sell'] + $allvat;

	$created = $silver['created'];
	$timestamp = strtotime($created);
	$new_date = date("d-m-Y H:i", $timestamp);
?>
<div class="card mb-2">
	<div class="card-body">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td colspan="4" class="text-center h4" style="height: 50px; overflow:hidden;"><label>ราคา แท่งเงิน ครั้งที่ <?php echo $silver['no'];?></label></td>
					</tr>
					<tr>
						<td colspan="2" class="text-center h5" style="height: 50px; overflow:hidden; vertical-align: middle; background-color: #C1272D; color:#fff;"><span style="height: 60px; overflow:hidden;">ราคาซื้อเข้า</span></td>
						<td colspan="2" class="text-center h5" style="height: 50px; overflow:hidden; vertical-align: middle; background-color: #009245; color:#fff; "><span style="height: 60px; overflow:hidden;">ราคาขายออก</span></td>
					</tr>
					<tr>
						<td colspan="2" class="text-center h5" style="height: 50px; overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($silver['buy'],2);?></span></td>
						<td colspan="2" class="text-center h5" style="height: 50px; overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($silver['sell'],2);?></span></td>
					</tr>
					<tr>
						<td colspan="4" class="text-center h5" style="height: 50px; overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;">วันที่ประกาศ <?php echo $new_date?></span></td>
					</tr>
					<tr>
						<td colspan="4" class="text-center h5" style="height: 50px; overflow:hidden;"><span style="height: 60px; overflow:hidden;">ราคารวม Vat+ <?php echo number_format($all,2);?></span></td>
					</tr>
				</tbody>
		
			</table>
	</div>
</div>
<?php }?>		
<div class="card mb-2">
	<div class="card-body">
			<table class="table table-bordered" style="width:100%">
				<thead class="bg-dark">
					<th class="text-center text-white" style="height: 50px;overflow:hidden; font-weight: bold;">วันที่</th>
					<th class="text-center text-white" style="height: 50px;overflow:hidden; font-weight: bold;">ครั้งที่</th>
					<th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาซื้อเข้า</th>
					<th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาขายออก</th>
					<th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาเปลี่ยนแปลง</th>					
				</thead>
				<?php
					$sql = "select * from bs_announce_silver where date =  '".$aa."' ORDER by no DESC limit 1 ";
					// $sql = "select * from bs_announce_silver where date =  '2023-05-26' ORDER by no DESC limit 1 ";
					$rss = $dbc->Query($sql);
					$lastdate = $dbc->Fetch($rss);
					$sql1 = "select *, (lag(buy, 1, '".$lastdate['buy']."') over (order by id)) as previos , (buy +- lag(buy, 1,'".$lastdate['buy']."') over (order by id)) as previosprice from bs_announce_silver WHERE date = '".$dd."' ORDER BY id DESC";

					$rsm = $dbc->Query($sql1);
					while($line = $dbc->Fetch($rsm)){	
							$created = $line['created'];
							$timestamp = strtotime($created);
							$new_date = date("d-m-Y H:i", $timestamp);
							$price = $line['previosprice'];
						
					
				?>
				<tbody>
					<tr>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $new_date;?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $line['no'];?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($line['buy'],2);?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($line['sell'],2);?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $price;?></span></td>
					</tr>
				</tbody>
				<?php } ?>
			</table>
	</div>
</div>
