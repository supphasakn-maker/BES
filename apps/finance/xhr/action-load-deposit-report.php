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
	$date3 = date("Y-m-d", strtotime("-2 day",strtotime($date)));
	$total = 0;
?>

<div class="card">
	<div class="card-header">รายละเอียดเงินโอนล่วงหน้า ตั้งแต่วันที่ <?php echo date("d/m/Y",strtotime($_POST['from']));?> ถึงวันที่ <?php echo date("d/m/Y",strtotime($_POST['to']));?></div>
	<div class="card-body">
		<table class="table table-sm table-bordered">
			<tbody>
				<tr>
                    <td colspan="2" class="font-weight-bold">รายละเอียด ยอดเงินโอนล่วงหน้า ตั้งแต่วันที่ <?php echo $date2;?></td>
                </tr>
				<?php
					$sql = "SELECT bs_payments.date_active,bs_payment_deposits.customer_id,bs_payment_deposits.id,bs_payment_deposits.amount,bs_payments.ref  FROM bs_payments
					LEFT OUTER JOIN bs_payment_deposits ON bs_payments.id = bs_payment_deposits.payment_id
					where bs_payment_deposits.status = 1 and bs_payments.ref LIKE '%โอนล่วงหน้า%' AND YEAR(date_active) > 2022  AND DATE(date_active) < '".$_POST['from']."'";
					$rst = $dbc->Query($sql);
					while($deposit = $dbc->Fetch($rst)){
						$customer = $dbc->GetRecord("bs_customers","*","id=".$deposit['customer_id']);
						$deposit_used = $dbc->GetRecord("bs_payment_deposit_use","SUM(amount)","deposit_id=".$deposit['id']);
						$use = $deposit['amount']-$deposit_used[0];
						echo '<tr>';
							echo '<td class="text-center" colspan="2">'.$customer['name'].'</td>';
							echo '<td class="text-right">'.number_format($deposit['amount'],2).'</td>';
						echo '</tr>';
					}
					$sql = "SELECT
					bs_payment_deposit_use.amount,
					bs_payment_deposit_use.customer_id 
					FROM bs_payment_deposit_use 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposit_use.payment_id
					WHERE DATE(bs_payments.date_active)  BETWEEN '".$date3."' AND '".$date2."'";
					$rst2 = $dbc->Query($sql);
				
					while($line = $dbc->Fetch($rst2)){
					if(isset($line[1])){
						$customer = $dbc->GetRecord("bs_customers","*","id=".$line[1]);
					}else{
						$customer = array("name" => "-");
					}

					$total -= $line[0];
					echo '<tr>';
						echo '<td class="text-center" colspan="2">'.$customer['name'].'</td>';
						echo '<td class="text-right pr-2">'.number_format($line[0],2).'</td>';
					echo '</tr>';
					}
					$sql = "SELECT SUM(bs_payment_deposits.amount) AS amount  FROM bs_payments
					LEFT OUTER JOIN bs_payment_deposits ON bs_payments.id = bs_payment_deposits.payment_id
					where bs_payment_deposits.status = 1 and bs_payments.ref LIKE '%โอนล่วงหน้า%' AND YEAR(date_active) > 2022  AND DATE(date_active) < '".$_POST['from']."'";
					$rst = $dbc->Query($sql);
					$line_deposit = $dbc->Fetch($rst);

					$sql = "SELECT SUM(bs_payment_deposit_use.amount) AS amount
					FROM bs_payment_deposit_use 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposit_use.payment_id
					WHERE DATE(bs_payments.date_active)  BETWEEN '".$date3."' AND '".$date2."'";
					$rst = $dbc->Query($sql);
					$line = $dbc->Fetch($rst);

					$linetotal = $line_deposit[0] + $line[0];

					echo '<tr>';
					echo '<td class="font-weight-bold text-center" colspan="2">รวม </td>';
					echo '<td class="text-right pr-2">'.number_format($linetotal,2).'</td>';
					echo '<tr>';


					$sql = "SELECT 
					SUM(bs_payment_deposit_use.amount) AS amount
					FROM bs_payment_deposit_use 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposit_use.payment_id
					WHERE DATE(bs_payments.datetime) BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'";
					$rst2 = $dbc->Query($sql);
					while($line = $dbc->Fetch($rst2)){
						echo '<tr>';
						echo '<td class="font-weight-bold" colspan="2">หัก โอนล่วงหน้า  </td>';
							echo '<td class="text-right pr-2"  style="color:#FF2400;">['.number_format($line['amount'],2).']</td>';
						echo '</tr>';
						$total -= $line['amount'];
					}
					$sql = "SELECT
					SUM(bs_payment_deposits.amount) AS amount
					FROM bs_payment_deposits 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposits.payment_id
					WHERE DATE(bs_payments.datetime) BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'";
					$rst = $dbc->Query($sql);
					while($line = $dbc->Fetch($rst)){
						echo '<tr>';
							echo '<td class="font-weight-bold" colspan="2">บวก โอนล่วงหน้า </td>';
							echo '<td class="text-right pr-2">'.number_format($line['amount'],2).'</td>';
						echo '<tr>';
						$total += $line['amount'];
					}

					$sql = "SELECT SUM(bs_payment_deposits.amount) AS amount  FROM bs_payments
					LEFT OUTER JOIN bs_payment_deposits ON bs_payments.id = bs_payment_deposits.payment_id
					where bs_payment_deposits.status = 1 and bs_payments.ref LIKE '%โอนล่วงหน้า%' AND YEAR(date_active) > 2022 AND DATE(date_active) < '".$_POST['to']."' ";
					$rst = $dbc->Query($sql);
					$line_deposit = $dbc->Fetch($rst);

					$sql = "SELECT
					SUM(bs_payment_deposits.amount) AS amount
					FROM bs_payment_deposits 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposits.payment_id
					WHERE DATE(bs_payments.datetime) = '".$_POST['to']."'";
					$rst = $dbc->Query($sql);
					$line = $dbc->Fetch($rst);

					$linetotal = $line_deposit['0'] + $line[0];

					echo '<tr>';
					echo '<td class="font-weight-bold" colspan="2">เงินโอนล่วงหน้าคงเหลือ ณ วันที่ '.$_POST['to'].' </td>';
					echo '<td class="text-right pr-2">'.number_format($linetotal,2).'</td>';
					echo '<tr>';
					?>
				<tr>
                    <td colspan="2" class="font-weight-bold">รายละเอียด ลูกค้าโอนล่วงหน้าคงเหลือยกไป ตั้งแต่วันที่ <?php echo date("d/m/Y",strtotime($_POST['from']));?> ถึงวันที่ <?php echo date("d/m/Y",strtotime($_POST['to']));?> </td>
                </tr>

				<?php			
					$sql = "SELECT
					bs_payment_deposits.amount AS amount,
					bs_payment_deposits.customer_id 
					FROM bs_payment_deposits 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposits.payment_id
					WHERE DATE(bs_payments.datetime) = '".$_POST['to']."' ";
					$rst2 = $dbc->Query($sql);
					while($line = $dbc->Fetch($rst2)){
					if(isset($line[1])){
						$customer = $dbc->GetRecord("bs_customers","*","id=".$line[1]);
					}else{
						$customer = array("name" => "-");
					}

					$total += $line[0];
					echo '<tr>';
						echo '<td class="text-center" colspan="2">'.$customer['name'].'</td>';
						echo '<td class="text-right pr-2">'.number_format($line[0],2).'</td>';
					echo '</tr>';

					}
					$sql = "SELECT bs_payments.date_active,bs_payment_deposits.customer_id,bs_payment_deposits.id,bs_payment_deposits.amount,bs_payments.ref  FROM bs_payments
					LEFT OUTER JOIN bs_payment_deposits ON bs_payments.id = bs_payment_deposits.payment_id
					where bs_payment_deposits.status = 1 and bs_payments.ref LIKE '%โอนล่วงหน้า%' AND YEAR(date_active) > 2022 AND DATE(date_active) < '".$_POST['to']."' ";
					$rst = $dbc->Query($sql);
					while($deposit = $dbc->Fetch($rst)){
						$customer = $dbc->GetRecord("bs_customers","*","id=".$deposit['customer_id']);
						$deposit_used = $dbc->GetRecord("bs_payment_deposit_use","SUM(amount)","deposit_id=".$deposit['id']);
						$use = $deposit['amount']-$deposit_used[0];
						echo '<tr>';
							echo '<td class="text-center" colspan="2">'.$customer['name'].'</td>';
							echo '<td class="text-right">'.number_format($deposit['amount'],2).'</td>';
						echo '</tr>';
						
						
					}
					$sql = "SELECT SUM(bs_payment_deposits.amount) AS amount  FROM bs_payments
					LEFT OUTER JOIN bs_payment_deposits ON bs_payments.id = bs_payment_deposits.payment_id
					where bs_payment_deposits.status = 1 and bs_payments.ref LIKE '%โอนล่วงหน้า%' AND YEAR(date_active) > 2022 AND DATE(date_active) < '".$_POST['to']."' ";
					$rst = $dbc->Query($sql);
					$line_deposit = $dbc->Fetch($rst);

					$sql = "SELECT
					SUM(bs_payment_deposits.amount) AS amount
					FROM bs_payment_deposits 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposits.payment_id
					WHERE DATE(bs_payments.datetime) = '".$_POST['to']."'";
					$rst = $dbc->Query($sql);
					$line = $dbc->Fetch($rst);

					$linetotal = $line_deposit[0] + $line[0];

					echo '<tr>';
					echo '<td class="font-weight-bold text-center" colspan="2">รวม </td>';
					echo '<td class="text-right pr-2">'.number_format($linetotal,2).'</td>';
					echo '<tr>';
									
				?>
			
			</tbody>
		<table>
	</div>
</div>