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
	$monthly = $_POST['month'];
    $subtitle = "ประจำเดือน ".$monthly;
    ?>
    <table class="table table-sm table-bordered table-striped" id="tblDeposit">
    <thead>
        <tr>
        <th class="text-center font-weight-bold">วันที่ใช้เบ้า</th>
            <th class="text-center font-weight-bold">หมายเลขเบ้า</th>
            <th class="text-center font-weight-bold">จำนวนครั้งที่ใช้ทั้งหมด</th>
            <th class="text-center font-weight-bold">รอบการผลิต</th>
            <th class="text-center font-weight-bold">จำนวนผลิต / KG</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0,0,0);
        $sql = "SELECT DISTINCT(bs_productions_crucible.round),bs_productions_furnace.date,bs_productions_furnace.amount,bs_productions_crucible.round,bs_productions_furnace.crucible 
        FROM bs_productions_furnace
        LEFT OUTER JOIN bs_productions_crucible ON bs_productions_furnace.crucible = bs_productions_crucible.round  
        WHERE DATE_FORMAT(bs_productions_furnace.date,'%Y-%m') = '".$_POST['month']."'
        order by bs_productions_crucible.round + 0 , bs_productions_furnace.date DESC ";
        $rst = $dbc->Query($sql);
		while($order = $dbc->Fetch($rst)){

            $sum = $dbc->GetRecord("bs_productions_furnace","SUM(amount) AS amount","crucible =".$order['round']);
            $count = $dbc->GetRecord("bs_productions_furnace","count(crucible) AS crucible","crucible =".$order['round']);

        
            echo '<tr>';
                 echo '<td class="text-center">'.$order['date'].'</td>';
                 echo '<td class="text-center">'.$order['crucible'].'</td>';
                 echo '<td class="text-center">'.$count['crucible'].'</td>';
                 echo '<td>';
                 $sql = "SELECT bs_productions_furnace.crucible ,bs_productions.round FROM bs_productions_furnace
                 LEFT OUTER JOIN bs_productions ON bs_productions_furnace.round = bs_productions.id
                 WHERE 
                 crucible =".$order['round'];
                 $rst_driver = $dbc->Query($sql);
                 while($driver = $dbc->Fetch($rst_driver)){
                     echo '<span class="badge badge-warning font-weight-bold">'.$driver['round'].'</span>';
                 }
                 echo '<td class="text-center">'.number_format($order['amount'],4).'</td>';
                 
            echo '</tr>';
            $aSum[0] += 1;
            $aSum[1] += $count['crucible'];
            $aSum[2] += $order['amount'];
           
        }

        ?>
    </tbody>
    <tfoot>
		<tr>
            <th class="text-center" colspan="2">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th class="text-center">รวม <?php echo $aSum[1] ?> รายการ</th>
            <th class="text-center"></th>
			<th class="text-center">จำนวน <?php echo number_format($aSum[2],4)?> กิโลกรัม</th>
		</tr>
	</tfoot>
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