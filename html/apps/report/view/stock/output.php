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
	$subtitle = "ประจำปี ".$_POST['year'];
	$year = $_POST['year'];
?>
<section class="text-center">
	<h3><?php echo $title;?></h3>
	<p><?php echo $subtitle;?> </p>
</section>
<table class="table table-sm table-bordered table-striped">
	<thead>
		<tr>
			<td class="text-center" rowspan="2">ลำดับ</td>
			<th class="text-center" rowspan="2">ชื่อลูกค้า</th>
			<th class="text-center" colspan="12">ยอดขายต่อเดือน</th>
			<th class="text-center" rowspan="2">สรุป</th>
		</tr>
		<tr>
    <?php
      foreach($aMonth as $month){
        echo '<td class="text-center">'.$month.'</td>';
      }
    ?>
		</tr>
	</thead>
	<tbody>
	<?php
		$aSum = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$sql = "SELECT
			customer_id,
			bs_customers.name AS name,
      SUM(bs_orders.amount) as total,
      SUM(bs_orders.amount) as total
		FROM bs_orders
    LEFT JOIN bs_customers ON bs_customers.id = bs_orders.customer_id
    WHERE YEAR(date) = '".$_POST['year']."'
      AND bs_orders.parent IS NULL 
      AND bs_orders.status > -1
		GROUP BY customer_id
    HAVING SUM(bs_orders.amount) > 1200
    ORDER BY SUM(bs_orders.amount) DESC
		";
		$rst = $dbc->Query($sql);
    $number = 1;
		while($set = $dbc->Fetch($rst)){
			echo '<tr>';
      echo '<td class="text-center">'.$number.'</td>';
      echo '<td class="text-center">'.$set['name'].'</td>';
				echo '<td class="text-center">'.$set['name'].'</td>';
        for($m=0;$m<count($aMonth);$m++){
          $item = $dbc->GetRecord("bs_orders","SUM(amount)","customer_id=".$set['customer_id']." AND YEAR(date) = '".$_POST['year']."'       AND bs_orders.parent IS NULL 
		  AND bs_orders.status > -1 AND  MONTH(date) = '".($m+1)."'");
          $aSum[$m]+=$item[0];
          echo '<td class="text-right pr-2">'.number_format($item[0],2).'</td>';
        }
        $aSum[12]+=$set['total'];
				echo '<td class="text-right pr-2">'.number_format($set['total'],2).'</td>';
			echo '</tr>';
      $number++;;
		}
		
	?>
	</tbody>
	<tfoot>
		<tr>
			<th class="text-center" colspan="3">รวมทั้งหมด</th>
      <?php
        for($m=0;$m<count($aMonth);$m++){
          echo '<th class="text-right pr-2">'.number_format($aSum[$m],2).'</th>';
        }
        echo '<th class="text-right pr-2">'.number_format($aSum[12],2).'</th>';
      ?>
		</tr>
	</tfoot>
</table>
<?php
	$dbc->Close();
?>