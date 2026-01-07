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
			<th class="text-center">หมายเลขเบ้า</th>
			<?php
                foreach($aMonth as $month){
                    echo '<td class="text-center">'.$month.'</td>';
                }
            ?>
			<th class="text-center">Total</th>
		</tr>
	</thead>
    <tbody>
    <?php
    $aSum = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $sql = "SELECT bs_productions_crucible.round,bs_productions_furnace.date,bs_productions_furnace.amount,bs_productions_crucible.round,bs_productions_furnace.crucible 
    FROM bs_productions_furnace
    LEFT OUTER JOIN bs_productions_crucible ON bs_productions_furnace.crucible = bs_productions_crucible.round  
    WHERE YEAR(bs_productions_furnace.date) = '".$_POST['year']."'
    GROUP BY bs_productions_crucible.id ORDER BY  bs_productions_crucible.round + 0 , SUM(bs_productions_furnace.amount) DESC ";
    $rst = $dbc->Query($sql);
    $number = 1;
    while($set = $dbc->Fetch($rst)){
        echo '<tr>';
        echo '<td class="text-center">'.$set['crucible'].'</td>';
        $sum_year = 0;
    for($m=0;$m<count($aMonth);$m++){
        $item = $dbc->GetRecord("bs_productions_furnace","SUM(amount) AS amount","YEAR(date) = '".$_POST['year']."' AND DATE_FORMAT(bs_productions_furnace.date,'%m') = ".($m+1)." AND crucible =".$set['round']);
        echo '<td class="text-right pr-2">'.number_format($item[0],4).'</td>';
        $aSum[$m] += $item[0];
        $sum_year +=  $item[0];
    }
        $aSum[count($aMonth)] += $sum_year;
        echo '<td class="text-right pr-2">'.number_format($sum_year,4).'</td>';
    $number++;
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
        
        </tr>
    </tfoot>
</table>
</div>
