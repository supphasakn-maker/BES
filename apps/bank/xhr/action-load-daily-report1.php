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
	
	$date = $_POST['date'];
	
	$total = 0;
?>
<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
	<thead>
		<tr>
			<th class="text-center table-dark" colspan="10">ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y",strtotime($date));?></tjh>
		</tr>
		<tr>
			<th class="text-center">วันสั่ง</th>
			<th class="text-center">Delivery No.</th>
			<th class="text-center">ลูกค้า</th>
			<th class="text-center">kio</th>
			<th class="text-center">บาท/กิโล</th>
			<th class="text-center">ยอดรวม (vat)</th>
			<th class="text-center">ช่วงเวลา</th>
			<th class="text-center">คนส่ง</th>
		</tr>
	</thead>
	<!-- /Filter columns -->
	<tbody>
	<?php
		$total_amount =0;
		$total_net = 0;
		$sql = "SELECT 
				bs_orders.date AS date,
				bs_deliveries.delivery_date AS delivery_date,
				bs_deliveries.code AS code,
				bs_orders.customer_name AS customer_name,
				bs_orders.amount AS amount,
				bs_orders.price AS price,
				bs_orders.total AS total,
				bs_orders.vat AS vat,
				bs_orders.net AS net,
				bs_orders.delivery_time AS delivery_time
			FROM bs_deliveries
			LEFT JOIN bs_orders ON bs_orders.delivery_id =  bs_deliveries.id
			WHERE
				DATE(bs_deliveries.delivery_date) = '".$date."'
				AND bs_orders.status > 0
			ORDER BY bs_orders.delivery_date ASC
				";
		$rst = $dbc->Query($sql);
		while($order = $dbc->Fetch($rst)){
			echo '<tr>';
			/*
				echo '<td class="text-Center">'.date("d/m/Y",strtotime($date)).'</td>';
				echo '<td class="text-Center">'.$order['code'].'</td>';
				echo '<td class="text-Center">'.$order['customer_name'].'</td>';
				
				*/
				echo '<td>'.date("d/m/Y",strtotime($order['date'])).'</td>';
				echo '<td>'.$order['code'].'</td>';
				echo '<td>'.$order['customer_name'].'</td>';
				echo '<td class="text-right">'.number_format($order['amount'],4).'</td>';
				echo '<td class="text-right">'.number_format($order['price'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['net'],2).'</td>';
				echo '<td>'.$order['delivery_time'].'</td>';
				echo '<td>-</td>';
			echo '</tr>';
			$total_amount += $order['amount'];
			$total_net += $order['net'];
		}
		$total += $total_net;
	?>
	
	</tbody>
	<thead>
		<tr>
			<th colspan="3" class="text-right">รวม</th>
			<th class="text-right"><?php echo number_format($total_amount,2);?></th>
			<th class="text-center"></th>
			<th class="text-right"><?php echo number_format($total_net,2);?></th>
			<th colspan="2"></th>
		</tr>
	</thead>
</table>

<div class="card">
	<div class="card-header">
		ข้อมูล
	</div>
	<div class="card-body">
		<table class="table table-sm table-bordered">
			<tbody>
				<tr>
					<td>ยอดส่งของประจำวัน</td>
					<td></td>
					<td class="text-right pr-2"><?php echo number_format($total_net,2);?></td>
				</tr>
				<?php
					$line = $dbc->GetRecord("bs_payments","SUM(amount)","date_active = '".$date."' AND DATE(datetime) < '".$date."'");
					$total += $line[0];
				?>
                <tr>
					<td>ยอดขาย Bowins Design</td>
					<td></td>
					<td class="text-right pr-2 ">0.00</td>
				</tr>
				<tr>
					<td>บวก Cheque ขึ้นเงินของเก่า</td>
					<td></td>
					<td class="text-right pr-2 "><?php echo number_format($line[0],2);?></td>
				</tr>
                <?php
					$line = $dbc->GetRecord("bs_payments","SUM(amount)","DATE(datetime) = '".$date."' AND DATE(date_active) > '".$date."'");
					$total -= $line[0];
				?>
				<tr>
					<td>หัก Cheque ยังไม่ขึ้นเงินของวันนี้</td>
					<td></td>
					<td class="text-right pr-2"  style="color:#FF2400;">[<?php echo number_format($line[0],2);?>]</td>
				</tr>
                <?php
                    $line = $dbc->GetRecord("bs_bank_statement","SUM(amount)","date = '".$date."' AND type = 2 AND bank_id = 3");
                    $total -= $line[0];
                ?>
                <?php
                $sql = "SELECT * FROM bs_finance_static_values WHERE type = 1 AND start <= '$date' AND (end >= '$date' OR end IS NULL)";
                $rst = $dbc->Query($sql);
                while($line = $dbc->Fetch($rst)){
                    $total += $line['amount'];
                }

				?>

				<?php	
					$sql = "SELECT
                    SUM(bs_payment_deposit_use.amount) AS amount,
                    bs_payment_deposit_use.customer_id 
                FROM bs_payment_deposit_use 
                LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposit_use.payment_id
                WHERE DATE(bs_payments.datetime) = '".$date."'";
                $rst2 = $dbc->Query($sql);
                $dedeposit = $dbc->Fetch($rst2);
                    echo '<tr>';
                        echo '<td>หัก โอนล่วงหน้า</td>';
                        echo '<td></td>';
                        echo '<td class="text-right pr-2"  style="color:#FF2400;">['.number_format($dedeposit['amount'],2).']</td>';
                    echo '</tr>';
            

					$sql = "SELECT
						SUM(bs_payment_deposits.amount) amount,
						bs_payment_deposits.customer_id 
					FROM bs_payment_deposits 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposits.payment_id
					WHERE DATE(bs_payments.datetime) = '".$date."'";
					$rst2 = $dbc->Query($sql);
					$plusdeposit = $dbc->Fetch($rst2);
						echo '<tr>';
							echo '<td>บวก โอนล่วงหน้า</td>';
							echo '<td></td>';
							echo '<td class="text-right pr-2">'.number_format($plusdeposit['amount'],2).'</td>';
						echo '</tr>';
					
                    ?>
                    <tr>
                        <td>โอนเงินต่างแบงค์เข้า SCB</td>
                        <td></td>
                        <td class="text-right pr-2 ">0.00</td>
                    </tr>
                    <?php
                    $line = $dbc->GetRecord("bs_payments","SUM(amount)","DATE(datetime) = '".$date."' AND DATE(date_active) > '".$date."'");
                    $total -= $line[0];
                    $totalaa = $total_net-$line[0]-$dedeposit['amount']+$plusdeposit['amount'];

                    echo '<tr>';
                    echo '<td>ยอดเงินรวม</td>';
                    echo '<td></td>';
                    echo '<td class="text-right pr-2">'.number_format($totalaa,2).'</td>';
                    echo '</tr>';
                ?>

				<tr>
					<td>ลูกหนี้ค้างจ่าย</td>
					<td></td>
					<td class="text-right pr-2">0.00</td>
				</tr>	
                <?php
                    $sql = "SELECT * FROM bs_payment_types WHERE id = '1' OR id = '8' ";
					$rst = $dbc->Query($sql);
					while($type = $dbc->Fetch($rst)){
						$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount,
							bs_payments.customer_id
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) = '".$date."' AND bs_payment_items.type_id = ".$type['id'];
						$rst2 = $dbc->Query($sql);
						while($line = $dbc->Fetch($rst2)){
							$total += $line[0];
							echo '<tr>';
								echo '<td>บวก '.$type['name'].'</td>';
								echo '<td></td>';
								echo '<td class="text-right pr-2">'.number_format($line[0],2).'</td>';
								
								
							echo '</tr>';
						}
                    }  

                ?>
                <?php
                    $sql = "SELECT SUM(bs_payment_items.amount) AS amount, bs_payments.customer_id ,bs_payment_types.name as name1
                    FROM bs_payment_items 
                    LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
                    LEFT JOIN bs_payment_types ON bs_payment_types.id = bs_payment_items.type_id
                    WHERE DATE(bs_payments.datetime) = '".$date."' AND bs_payment_items.type_id = '8'";
                	$rst2 = $dbc->Query($sql);
                	$line = $dbc->Fetch($rst2);
                    $alltotal = $totalaa+$line['amount'];
				?>
				<tr>
					<td>ยอดรวมทั้งหมด</td>
					<td></td>
					<td class="text-right pr-2"><?php echo number_format($alltotal,2);?></td>
				</tr>
				<?php
					$line = $dbc->GetRecord("bs_bank_statement","SUM(amount)","date = '".$date."' AND type = 2 AND bank_id = 9");
				?>
			
			</tbody>
		<table>
	</div>
</div>