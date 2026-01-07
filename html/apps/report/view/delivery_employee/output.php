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
	$subtitle = "ประดือน ".date("F Y",strtotime($_POST['month']));

	$total_day = date("t",strtotime($_POST['month']))
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
			for($n=1;$n<=$total_day;$n++){
				echo '<th class="text-center">'.$n.'</th>';
			}
			?>
			<th class="text-center">Total</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$aSum = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		
		$sql = "SELECT
			DISTINCT(bs_employees.id) AS driver_id,
			bs_employees.nickname AS driver
		FROM bs_deliveries_drivers
    LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
    LEFT JOIN bs_deliveries ON bs_deliveries_drivers.delivery_id = bs_deliveries.id
    WHERE DATE_FORMAT(bs_deliveries.delivery_date,'%Y-%m') = '".$_POST['month']."'
     
			
		";
		$rst = $dbc->Query($sql);
    $number = 1;
		while($set = $dbc->Fetch($rst)){
			echo '<tr>';
				echo '<td class="text-center">'.$set['driver'].'</td>';
				$sum_in_month = 0;
				for($n=1;$n<=$total_day;$n++){
					$func = "fn.dialog.open('apps/report/view/delivery_employee/dialog.lookup.php','#dialog_lookup',{driver_id:".$set['driver_id'].",month:'".$_POST['month']."',date:".$n."})";
					$item = $dbc->GetRecord(
						"bs_deliveries_drivers LEFT JOIN bs_deliveries ON bs_deliveries_drivers.delivery_id = bs_deliveries.id",
						"COUNT(bs_deliveries_drivers.id)",
							"DATE_FORMAT(bs_deliveries.delivery_date,'%Y-%m') = '".$_POST['month']."' 
							AND DATE_FORMAT(bs_deliveries.delivery_date,'%e') = ".$n." 
							AND bs_deliveries_drivers.emp_driver = ".$set['driver_id']);
					echo '<td class="text-center pr-2"><a href="javascript:;" onclick="'.$func.'">'.number_format($item[0],0).'</a></td>';
					$aSum[$n-1] += $item[0];
					$sum_in_month +=  $item[0];
				}
				$aSum[$total_day] += $sum_in_month;
				echo '<th class="text-center pr-2">'.number_format($sum_in_month,0).'</th>';
				$number++;;
			echo '</tr>';
		}
		
	?>
	</tbody>
	<tfoot>
		<tr>
			<th class="text-center">รวม</th>
      <?php
      for($n=1;$n<=$total_day;$n++){
				echo '<td class="text-center pr-2">'.number_format($aSum[$n-1],0).'</td>';
			}
      echo '<th class="text-center pr-2">'.number_format($aSum[$total_day],0).'</th>';
      ?>
		</tr>
	</tfoot>
</table>
		</div>
<?php
	$dbc->Close();
?>