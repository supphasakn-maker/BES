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
	$year = $_POST['year'];
	$subtitle = "ประจำปี ".$year;
?>
<section class="text-center">
	<h3><?php echo $title;?></h3>
	<p><?php echo $subtitle;?> </p>
</section>
<div class="overflow-auto">
<table class="table table-sm table-bordered table-striped overflow-auto">
	<thead>
		<tr>
			<th class="text-center">ชื่อพนักงานขาย</th>
			<?php
      foreach($aMonth as $month){
        echo '<td class="text-center">'.$month.'</td>';
      }
    ?>
			<th class="text-center">Total</th>
			<th class="text-center">Target</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$aSum = array(0,0,0,0,0,0,0,0,0,0,0,0);
		
		$sql = "SELECT
			DISTINCT(bs_orders.sales) AS sales_id,
			bs_employees.nickname AS sales
		FROM bs_orders
    LEFT JOIN bs_employees ON bs_orders.sales = bs_employees.id
    WHERE YEAR(date) = '".$_POST['year']."'
      AND bs_orders.parent IS NULL 
      AND bs_orders.sales IS NOT NULL 
      AND bs_orders.status > -1
			
		";
		$rst = $dbc->Query($sql);
    $number = 1;
		while($set = $dbc->Fetch($rst)){
			echo '<tr>';
				echo '<td class="text-center">'.$set['sales'].'</td>';
				$sum_year = 0;
				for($m = 0; $m < count($aMonth); ++$m){
					$item = $dbc->GetRecord("bs_orders","SUM(amount)","YEAR(date) = '".$_POST['year']."' AND DATE_FORMAT(bs_orders.date,'%m') = ".($m+1)." 
					AND bs_orders.parent IS NULL  AND bs_orders.status > -1
						AND bs_orders.sales = ".$set['sales_id']);
					echo '<td class="text-right pr-2">'.number_format($item[0],4).'</td>';
					$aSum[$m] += $item[0];
					$sum_year +=  $item[0];
				}
				$aSum[count($aMonth)] += $sum_year;
				echo '<td class="text-right pr-2">'.number_format($sum_year,4).'</td>';
				$number++;;
			echo '</tr>';
		}
		
	?>
	</tbody>
	<tfoot>
		<tr>
			<th class="text-center">รวม</th>
      <?php
      for($i = 0; $i < count($aSum); $i++){
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