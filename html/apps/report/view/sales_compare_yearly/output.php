<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	include "../../include/const.php";

	$title = $aReportType[$_POST['type']];
	$years = array_reverse($_POST['year_list']);
	$subtitle = "ประจำปี ".join(",",$years);
?>
<section class="text-center">
	<h3><?php echo $title;?></h3>
	<p><?php echo $subtitle;?> </p>
</section>
<div class="overflow-auto">
<table class="table table-sm table-bordered table-striped overflow-auto">
	<thead>
		<tr>
			<th class="text-center" rowspan="2">ลำดับ</th>
			<th class="text-center" rowspan="2">ชื่อลูกค้า</th>
			
			<?php
			for($i=0;$i<count($years);$i++){
        echo '<td class="text-center" colspan="'.($i>0?3:2).'">'.$years[$i].'</td>';
      }
    	?>
		</tr>
		<tr>
			<?php
      for($i=0;$i<count($years);$i++){
        echo '<td class="text-center">Amount(KGS)</td>';
        echo '<td class="text-center">Total</td>';
				if($i>0)echo '<td class="text-center">Diff</td>';
      }
    	?>
		</tr>
	</thead>
	<tbody>
	<?php
		$aSum = array(0,0,0,0,0,0,0,0,0,0,0,0);
		
		$sql = "SELECT
			bs_customers.id AS customer_id,
			bs_customers.name AS customer_name
		FROM bs_orders
    LEFT JOIN bs_customers ON bs_customers.id = bs_orders.customer_id
    WHERE YEAR(date) in (".join(",",$years).")
      AND bs_orders.parent IS NULL 
      AND bs_orders.sales IS NOT NULL 
      AND bs_orders.status > -1
		GROUP BY bs_orders.customer_id
		HAVING SUM(bs_orders.amount) > 0
		ORDER BY SUM(amount) DESC;
		";
		$rst = $dbc->Query($sql);
    $number = 1;
		while($set = $dbc->Fetch($rst)){
			echo '<tr>';
				echo '<td class="text-center">'.$number.'</td>';
				echo '<td class="text-center">'.$set['customer_name'].'</td>';

				$previous = 0;
				for($i=0;$i<count($years);$i++){
					$year = $years[$i];
					$item = $dbc->GetRecord("bs_orders","SUM(amount),SUM(total)","YEAR(date) = '".$year."' AND bs_orders.parent IS NULL 
					AND bs_orders.sales IS NOT NULL 
					AND bs_orders.status > -1 AND customer_id = ".$set['customer_id']);
					echo '<td class="text-right pr-2">'.number_format($item[0],4).'</td>';
					echo '<td class="text-right pr-2">'.number_format($item[1],2).'</td>';
					if($i>0){
						echo '<td class="text-right text-primary pr-2">'.number_format($item[0]-$previous,4).'</td>';
					}
					$previous = $item[0];
				}
				$number++;;
			echo '</tr>';
		}
		
	?>
	</tbody>
	<tfoot>
		<tr>
			<th class="text-center">รวม</th>
      <?php
	  for($i = 0; $i < count($aSum); ++$i) {
				echo '<td class="text-right pr-2">'.number_format($aSum[$i],4).'</td>';
			}
      ?>
			<th class="text-center" colspan="2"></th>
		</tr>
	</tfoot>
</table>
		</div>
<?php
	$dbc->Close();
?>