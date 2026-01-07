<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$total = 0;
?>
<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
	<thead>
		<tr>
			<th class="text-center table-dark" colspan="10">ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y", strtotime($_POST['date_from'])); ?> ถึงวันที่ <?php echo date("d/m/Y", strtotime($_POST['date_to'])); ?> </th>
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
		$total_amount = 0;
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
			WHERE bs_deliveries.delivery_date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_orders.status > 0 
			ORDER BY bs_orders.delivery_date ASC";
		$rst = $dbc->Query($sql);

		while ($order = $dbc->Fetch($rst)) {
			echo '<tr>';

			echo '<td>' . date("d/m/Y", strtotime($order['date'])) . '</td>';
			echo '<td>' . $order['code'] . '</td>';
			echo '<td>' . $order['customer_name'] . '</td>';
			echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
			echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
			echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
			echo '<td>' . $order['delivery_time'] . '</td>';
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
			<th class="text-right"><?php echo number_format($total_amount, 2); ?></th>
			<th class="text-center"></th>
			<?php $ss = floor($total_net * 100) / 100; ?>
			<th class="text-right"><?php echo number_format($ss, 2); ?></th>
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
					<td>ยอดส่งของรวม VAT</td>
					<td></td>
					<td class="text-right pr-2"><?php echo number_format($ss, 2); ?></td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_payments", "SUM(amount)", "date_active = '" . $_POST['date_from'] . "' AND DATE(datetime) < '" . $_POST['date_from'] . "'");
				$total += $line[0];
				?>
				<tr>
					<td>บวก Cheque ขึ้นเงินของเก่า</td>
					<td></td>
					<td class="text-right pr-2 "><?php echo number_format($line[0], 2); ?></td>
				</tr>
				<?php
				$sql = "SELECT * FROM bs_finance_static_values WHERE type = 1 AND start <= '" . $_POST['date_to'] . "' AND (end >= '" . $_POST['date_to'] . "' OR end IS NULL)";
				$rst = $dbc->Query($sql);
				while ($line = $dbc->Fetch($rst)) {
					echo '<tr>';
					echo '<td class="pl-2">บวก ' . $line['title'] . '</td>';
					echo '<td>' . $line['customer_name'] . '</td>';
					echo '<td class="text-right pr-2">' . number_format($line['amount'], 2) . '</td>';
					echo '<tr>';
					$total += $line['amount'];
				}
				?>
				<?php
				$line = $dbc->GetRecord("bs_payments", "SUM(amount)", "DATE(datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND DATE(date_active) > '" . $_POST['date_to'] . "'");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก Cheque ยังไม่ขึ้นเงินของวันนี้</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 3");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า BBL</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 7");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า KBANK</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 11");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า UOB</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 10");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า KTB</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 6");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า BAY</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 15");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า SCB-BWF</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 13");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า SCB(USD)</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 14");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า SCB S/A</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 12");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า KBANK(BWD)</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 16");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า BBL(USD)</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 17");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า BBL-BWF</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 18");
				$total -= $line[0];
				?>
				<tr>
					<td>หัก โอนเข้า KBANK (USD)</td>
					<td></td>
					<td class="text-right pr-2" style="color:#FF2400;">[<?php echo number_format($line[0], 2); ?>]</td>
				</tr>
				<?php
				$aTransfer = array(
					array("KBank Huamark -> SCB Ram 24", 7, 9),
					array("KBank Huamark -> BBL Ram 28", 7, 3),
					array("KBank Huamark BWD -> SCB Ram 24", 12, 9),
					array("KBank Huamark BWD -> BBL Ram 28", 12, 3),
					array("KBank Huamark BWD -> BAY Huamark", 12, 6),
					array("KBank Huamark BWD -> UOB Huamark", 12, 11),
					array("KBank Huamark BWD -> KTB Huamark", 12, 10),
					array("KBank Huamark BWD -> KBank Huamark", 12, 7),
					array("KBank Huamark -> BAY Huamark", 7, 6),
					array("UOB Huamark -> SCB Ram 24", 11, 9),
					array("UOB Huamark -> BBL Ram 28", 11, 3),
					array("KTB Huamark -> SCB Ram 24", 10, 9),
					array("KTB Huamark -> BBL Ram 28", 10, 3),
					array("BAY Huamark -> SCB Ram 24", 6, 9),
					array("BAY Huamark -> BBL Ram 28", 6, 3),
					array("KBank Huamark -> KTB Huamark", 7, 10),
					array("KBank Huamark -> UOB Huamark", 7, 11),
					array("SCB Ram 24 -> BBL Ram 28", 9, 3),
					array("BBL Ram 28 -> SCB Ram 24", 3, 9),
					array("SCB S/A -> SCB Ram 24", 14, 9),
					array("BBL Ram 28 -> KBank Huamark ", 3, 7),
					array("SCB BWF -> SCB Ram 24 ", 15, 9),
					array("KBank Huamark BWD -> SCB BWF", 12, 15),
					array("SCB Ram 24 -> KBank Huamark", 9, 7),
					array("BBL Ram 28 -> BBL (USD)", 3, 16),
					array("KBank Huamark -> SCB BWF", 7, 15),
					array("BAY Huamark -> KBank Huamark", 6, 7),
					array("SCB Ram 24 -> BAY Huamark", 9, 6),
					array("SCB S/A -> SCB BWF", 14, 15),
					array("BBL-BWF -> BBL Ram 28", 17, 3),
					array("BBL-BWF -> SCB BWF", 17, 15)
				);

				foreach ($aTransfer as $tr) {
					$sql = "SELECT -SUM(TableA.amount) 
							FROM bs_bank_statement TableA
							LEFT JOIN bs_bank_statement TableB ON TableA.transfer_to = TableB.id
							WHERE
								TableA.date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND TableA.type = 1
								AND TableA.bank_id = $tr[1]
								AND TableB.bank_id = $tr[2]
						";
					$rst = $dbc->Query($sql);
					$item = $dbc->Fetch($rst);
					if ($item[0] == 0) {
						$class = "display: none;";
					} else {
						echo '<tr>';
						echo '<td>บวก ' . $tr[0] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($item[0], 2) . '</td>';
						echo '</tr>';
					}

					$total += $item[0];
				}

				$sql = "SELECT SUM(bs_payment_deposits.amount) AS amount
					
					FROM bs_payment_deposits 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposits.payment_id
					WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' ";
				$rst2 = $dbc->Query($sql);
				while ($line = $dbc->Fetch($rst2)) {
					if (isset($line[1])) {
						$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
					} else {
						$customer = array("name" => "-");
					}

					$total += $line[0];
					echo '<tr>';
					echo '<td>บวก โอนล่วงหน้า</td>';
					echo '<td></td>';
					echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';
					echo '</tr>';
				}

				$sql = "SELECT SUM(bs_payment_deposit_use.amount) AS amount
					FROM bs_payment_deposit_use 
					LEFT JOIN bs_payments ON bs_payments.id = bs_payment_deposit_use.payment_id
					WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' ";
				$rst2 = $dbc->Query($sql);
				while ($line = $dbc->Fetch($rst2)) {
					if (isset($line[1])) {
						$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
					} else {
						$customer = array("name" => "-");
					}

					$total -= $line[0];
					echo '<tr>';
					echo '<td>ลบ โอนล่วงหน้า</td>';
					echo '<td></td>';
					echo '<td class="text-right pr-2"  style="color:#FF2400;">[' . number_format($line[0], 2) . ']</td>';
					echo '</tr>';
				}

				$sql = "SELECT * FROM bs_payment_types WHERE id =1";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =2";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =3";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =4";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =5";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =6";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =7";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =8";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =9";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =10";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =11";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =12";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก ' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				$sql = "SELECT * FROM bs_payment_types WHERE id =13";
				$rst = $dbc->Query($sql);
				while ($type = $dbc->Fetch($rst)) {
					$sql = "SELECT
							SUM(bs_payment_items.amount) AS amount
						FROM bs_payment_items 
						LEFT JOIN bs_payments ON bs_payments.id = bs_payment_items.payment_id
						WHERE DATE(bs_payments.datetime) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_payment_items.type_id = " . $type['id'];
					$rst2 = $dbc->Query($sql);
					while ($line = $dbc->Fetch($rst2)) {


						if (isset($line[1])) {
							$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $line[1]);
						} else {
							$customer = array("name" => "-");
						}

						$total += $line[0];
						echo '<tr>';
						echo '<td>บวก โอน' . $type['name'] . '</td>';
						echo '<td></td>';
						echo '<td class="text-right pr-2">' . number_format($line[0], 2) . '</td>';


						echo '</tr>';
					}
				}
				?>

				<?php
				$sql = "SELECT * FROM bs_finance_static_values WHERE type = 2 AND start <=  '" . $_POST['date_to'] . "' AND (end >= '" . $_POST['date_to'] . "' OR end IS NULL)";
				$rst = $dbc->Query($sql);
				while ($line = $dbc->Fetch($rst)) {
					echo '<tr>';
					echo '<td>ลบ ' . $line['title'] . '</td>';
					echo '<td>' . $line['customer_name'] . '</td>';
					echo '<td class="text-right pr-2" style="color:#FF2400;">[' . number_format($line['amount'], 2) . ']</td>';

					echo '<tr>';
					$total -= $line['amount'];
				}
				?>


				<tr>
					<td>ยอดรวมทั้งหมด</td>
					<td></td>
					<?php $total1 = floor($total * 100) / 100; ?>
					<td class="text-right pr-2"><?php echo number_format($total1, 2); ?></td>
				</tr>
				<?php
				$line = $dbc->GetRecord("bs_bank_statement", "SUM(amount)", "date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND type = 2 AND bank_id = 9");
				?>
				<tr>
					<td>ยอด โอนเข้า SCB</td>
					<td></td>
					<td class="text-right pr-2"><?php echo number_format($line[0], 2); ?></td>
				</tr>

				<tr>
					<td>Balance</td>
					<td></td>
					<?php echo
					$ss = floor($total * 100) / 100;
					$aa = floor($line[0] * 100) / 100;

					$totalweek = $ss - $aa;
					?>
					<td class="text-right pr-2"><?php echo number_format($totalweek, 2); ?></td>
				</tr>

			</tbody>
			<table>
	</div>
</div>