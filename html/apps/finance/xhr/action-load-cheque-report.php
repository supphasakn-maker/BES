<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	$date = $_POST['from'];

    $date2 = date("Y-m-d", strtotime("-1 day",strtotime($date)));

	
	$total = 0;
?>


<div class="card">
	<div class="card-header">รายละเอียดลูกค้า Chq.+Cash ที่ยังไม่ขึ้นเงิน ตั้งแต่วันที่ <?php echo date("d/m/Y",strtotime($_POST['from']));?> ถึงวันที่ <?php echo date("d/m/Y",strtotime($_POST['to']));?></div>
	<div class="card-body">
		<table class="table table-sm table-bordered">
			<tbody>
				<?php
					$line = $dbc->GetRecord("bs_payments","SUM(amount)","date_active = '".$_POST['from']."' AND DATE(datetime) < '".$_POST['from']."'");
					$total += $line[0];
				?>
				<tr>
					<td  class="font-weight-bold">เช็คที่ยังไม่เข้าบัญชียกมา <?php echo $date2;?></td>
					<td class="text-right pr-2 "><?php echo number_format($line[0],2);?></td>
				</tr>
                <tr>
                    <td colspan="2" class="font-weight-bold">รายละเอียด เช็คที่ยังไม่เข้าบัญชียกมา</td>
                </tr>
                <?php
					$sql = "SELECT bs_customers.name, bs_payments.id,bs_payments.code,bs_payments.amount,bs_payments.customer_id FROM bs_payments 
                    LEFT OUTER JOIN bs_customers ON bs_payments.customer_id = bs_customers.id
                    WHERE  bs_payments.payment LIKE '%clearing%' AND DATE(date_active) = '".$date."'  AND DATE(datetime) < '".$date."' ";

					$rst = $dbc->Query($sql);
					while($line = $dbc->Fetch($rst)){
						echo '<tr>';
							echo '<td class="text-center">'.$line['name'].'</td>';
							echo '<td class="text-right pr-2">'.number_format($line['amount'],2).'</td>';
						echo '<tr>';
						$total += $line['amount'];
					}
				?>
				<?php
					$line = $dbc->GetRecord("bs_payments","SUM(amount)","DATE(datetime) BETWEEN '".$_POST['from']."' AND '".$_POST['to']."' AND DATE(date_active) > '".$_POST['to']."'");
					$total -= $line[0];
				?>
				<tr>
					<td class="font-weight-bold">หัก Cheque ยังไม่ขึ้นเงินของวันนี้</td>
					<td class="text-right pr-2"  style="color:#FF2400;">[<?php echo number_format($line[0],2);?>]</td>
				</tr>
				<?php
					$line = $dbc->GetRecord("bs_bank_statement","SUM(amount)","date BETWEEN '".$_POST['from']."' AND '".$_POST['to']."' AND type = 2 AND bank_id = 3");
					$total -= $line[0];
				?>
                <tr>
                    <td colspan="2" class="font-weight-bold">รายละเอียด Chq.+Cash ที่ยังไม่ขึ้นเงิน</td>
                </tr>
                <?php
					$sql = "SELECT bs_customers.name, bs_payments.id,bs_payments.code,bs_payments.amount,bs_payments.customer_id FROM bs_payments 
                    LEFT OUTER JOIN bs_customers ON bs_payments.customer_id = bs_customers.id
                    WHERE DATE(datetime) BETWEEN '".$_POST['from']."' AND '".$_POST['to']."' AND DATE(date_active) > '".$_POST['to']."'";
					$rst = $dbc->Query($sql);
					while($line = $dbc->Fetch($rst)){
						echo '<tr>';
							echo '<td class="text-center">'.$line['name'].'</td>';
							echo '<td class="text-right pr-2">'.number_format($line['amount'],2).'</td>';
						echo '<tr>';
						$total += $line['amount'];
					}
				?>
                <?php
					$line = $dbc->GetRecord("bs_payments","SUM(amount)","DATE(datetime) BETWEEN '".$_POST['from']."' AND '".$_POST['to']."' AND DATE(date_active) > '".$_POST['to']."'");
					$total -= $line[0];
				?>
                <tr>
					<td class="font-weight-bold text-center">รวม</td>
					<td class="text-right pr-2 font-weight-bold"><?php echo number_format($line[0],2);?></td>
				</tr>
    
			</tbody>
		<table>
	</div>
</div>