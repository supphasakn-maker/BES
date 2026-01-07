<?php
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// ====== ภาพรวมทั้งหมด (ไม่กรองสโตร์) ======
$sql = "SELECT * FROM bs_orders WHERE DATE(date) LIKE '" . $date . "' AND bs_orders.status > 0";
$rst = $dbc->Query($sql);

$thb_orders = array();
$usd_orders = array();

$thb_total_amount = 0;
$thb_total_vat = 0;
$thb_total_total = 0;
$thb_total_net = 0;
$thb_counter = 0;

$usd_total_amount = 0;
$usd_total_vat = 0;
$usd_total_total = 0;
$usd_total_net = 0;
$usd_total_usd = 0;
$usd_counter = 0;

while ($order = $dbc->Fetch($rst)) {
	if ($order['usd'] > 0) {
		$usd_orders[] = $order;
	} else {
		$thb_orders[] = $order;
	}
}
?>

<style>
	.sales-table-responsive {
		overflow-x: auto;
		-webkit-overflow-scrolling: touch;
		margin-bottom: 1rem;
		position: relative;
	}

	.sales-table-responsive .table-sm td,
	.sales-table-responsive .table-sm th {
		padding: 0.3rem;
		font-size: 0.9rem;
		white-space: nowrap;
	}

	@media (min-width: 768px) and (max-width: 1024px) {

		.sales-table-responsive .table-sm td,
		.sales-table-responsive .table-sm th {
			font-size: 0.9rem;
			padding: 0.25rem;
		}

		.summary-cards .col-md-4 {
			margin-bottom: 1rem;
		}
	}

	@media (max-width: 767px) {
		.sales-table-responsive .table-sm {
			font-size: 0.9rem;
		}

		.sales-table-responsive .table-sm td,
		.sales-table-responsive .table-sm th {
			padding: 0.2rem;
			font-size: 0.65rem;
		}

		.sales-table-responsive .hide-mobile {
			display: none;
		}

		.summary-cards .col-md-4 {
			margin-bottom: 1rem;
		}

		.summary-cards .card-body p {
			font-size: 0.85rem;
			margin-bottom: 0.5rem;
		}
	}

	.sales-table-responsive::after {
		content: '← เลื่อนดูเพิ่ม →';
		position: absolute;
		bottom: 5px;
		right: 5px;
		background: rgba(0, 32, 78, 0.9);
		color: white;
		padding: 0.25rem 0.5rem;
		font-size: 0.7rem;
		border-radius: 3px;
		opacity: 0;
		transition: opacity 0.3s;
		pointer-events: none;
	}

	@media (max-width: 1024px) {
		.sales-table-responsive::after {
			opacity: 1;
		}

		.sales-table-responsive.scrolled::after {
			opacity: 0;
		}
	}

	.summary-cards {
		margin-top: 1rem;
	}

	.summary-cards .card {
		margin-bottom: 1rem;
		height: 100%;
	}

	.summary-cards .card-body p {
		margin-bottom: 0.75rem;
		line-height: 1.6;
	}

	.summary-cards .card-body p:last-child {
		margin-bottom: 0;
	}

	.btn-xs {
		padding: 0.15rem 0.3rem;
		font-size: 0.75rem;
	}

	@media (max-width: 767px) {
		.btn-xs {
			padding: 0.2rem 0.4rem;
			font-size: 0.7rem;
		}

		.btn-xs i {
			font-size: 0.8rem;
		}
	}

	/* Header แยกสโตร์ให้อ่านง่ายขึ้น */
	.section-heading {
		margin: 1.5rem 0 0.5rem;
		font-weight: 700;
		font-size: 1.05rem;
	}
</style>

<!-- ======================= ภาพรวมการขายทั้งหมด (THB) ======================= -->
<div class="sales-table-responsive">
	<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
		<thead>
			<tr>
				<th class="text-center table-dark font-weight-bold" colspan="12">
					ภาพรวมการขาย (THB) ประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?>
				</th>
			</tr>
			<tr>
				<th class="text-center table-dark font-weight-bold">PO</th>
				<th class="text-center table-dark font-weight-bold">CUSTOMER</th>
				<th class="text-center table-dark font-weight-bold">KIO</th>
				<th class="text-center table-dark font-weight-bold">BATH/KGS</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">VAT</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">TOTAL</th>
				<th class="text-center table-dark font-weight-bold">TOTAL+VAT</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">DELIVERY</th>
				<th class="text-center table-dark font-weight-bold">SALE</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">SP</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">EX</th>
				<th class="text-center table-dark font-weight-bold">DEL</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($thb_orders as $order) {
				$bgsuccess = ($order['product_id'] == '4') ? 'class="bg-success text-white font-weight-bold"' : '';
				echo '<tr ' . $bgsuccess . '>';
				echo '<td class="text-left">' . $order['code'] . '</td>';
				echo '<td class="text-left">' . $order['customer_name'] . '</td>';
				echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
				echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
				echo '<td class="text-right hide-mobile">' . number_format($order['vat'], 2) . '</td>';
				echo '<td class="text-right hide-mobile">' . number_format($order['total'], 2) . '</td>';
				echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
				echo '<td class="text-center hide-mobile">';
				if ($order['delivery_date'] == null) {
					echo '<span class="badge badge-danger">lock</span>';
				} else {
					echo date("d/m/y", strtotime($order['delivery_date']));
				}
				echo '</td>';
				echo '<td class="text-left">';
				if ($order['sales'] != "") {
					$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
					echo $employee['fullname'];
				} else {
					echo "-";
				}
				echo '</td>';
				echo '<td class="text-right hide-mobile">' . number_format($order['rate_spot'], 2) . '</td>';
				echo '<td class="text-right hide-mobile">' . number_format($order['rate_exchange'], 2) . '</td>';
				echo '<td class="text-center">';
				echo '<button onclick="fn.app.sales.order.dialog_remove_each(' . $order['id'] . ')" class="btn btn-xs btn-outline-danger mr-1 btn-icon"><i class="fa fa-trash"></i></button>';
				echo '</td>';
				echo '</tr>';

				$thb_total_amount += $order['amount'];
				$thb_total_vat += $order['vat'];
				$thb_total_total += $order['total'];
				$thb_total_net += $order['net'];
				$thb_counter++;
			}
			?>
		</tbody>
		<tfoot>
			<tr class="table-warning">
				<th class="text-center" colspan="2">รายการ <?php echo $thb_counter; ?> รายการ (THB)</th>
				<th class="text-center"><?php echo number_format($thb_total_amount, 4); ?></th>
				<th class="text-center"></th>
				<th class="text-center hide-mobile"><?php echo number_format($thb_total_vat, 2); ?></th>
				<th class="text-center hide-mobile"><?php echo number_format($thb_total_total, 2); ?></th>
				<th class="text-center"><?php echo number_format($thb_total_net, 2); ?></th>
				<th class="text-center" colspan="5"></th>
			</tr>
		</tfoot>
	</table>
</div>

<?php if (count($usd_orders) > 0) { ?>
	<div class="mt-3">
		<div class="sales-table-responsive">
			<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
				<thead>
					<tr>
						<th class="text-center table-warning font-weight-bold" colspan="13">
							ภาพรวมการขาย (USD) ประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?>
						</th>
					</tr>
					<tr>
						<th class="text-center table-warning font-weight-bold">PO</th>
						<th class="text-center table-warning font-weight-bold">CUSTOMER</th>
						<th class="text-center table-warning font-weight-bold">KIO</th>
						<th class="text-center table-warning font-weight-bold">BATH/KGS</th>
						<th class="text-center table-warning font-weight-bold hide-mobile">VAT</th>
						<th class="text-center table-warning font-weight-bold hide-mobile">TOTAL</th>
						<th class="text-center table-warning font-weight-bold">TOTAL+VAT</th>
						<th class="text-center table-warning font-weight-bold">USD</th>
						<th class="text-center table-warning font-weight-bold hide-mobile">DELIVERY</th>
						<th class="text-center table-warning font-weight-bold">SALE</th>
						<th class="text-center table-warning font-weight-bold hide-mobile">SP</th>
						<th class="text-center table-warning font-weight-bold hide-mobile">EX</th>
						<th class="text-center table-warning font-weight-bold">DEL</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($usd_orders as $order) {
						$bgsuccess = ($order['product_id'] == '4') ? 'class="bg-success text-white font-weight-bold"' : '';
						echo '<tr ' . $bgsuccess . '>';
						echo '<td class="text-center">' . $order['code'] . '</td>';
						echo '<td class="text-center">' . $order['customer_name'] . '</td>';
						echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
						echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
						echo '<td class="text-right hide-mobile">' . number_format($order['vat'], 2) . '</td>';
						echo '<td class="text-right hide-mobile">' . number_format($order['total'], 2) . '</td>';
						echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
						echo '<td class="text-right">' . number_format($order['usd'], 2) . '</td>';
						echo '<td class="text-center hide-mobile">';
						if ($order['delivery_date'] == null) {
							echo '<span class="badge badge-danger">lock</span>';
						} else {
							echo date("d/m/y", strtotime($order['delivery_date']));
						}
						echo '</td>';
						echo '<td class="text-center">';
						if ($order['sales'] != "") {
							$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
							echo $employee['fullname'];
						} else {
							echo "-";
						}
						echo '</td>';
						echo '<td class="text-right hide-mobile">' . number_format($order['rate_spot'], 2) . '</td>';
						echo '<td class="text-right hide-mobile">' . number_format($order['rate_exchange'], 2) . '</td>';
						echo '<td class="text-center">';
						echo '<button onclick="fn.app.sales.order.dialog_remove_each(' . $order['id'] . ')" class="btn btn-xs btn-outline-danger mr-1 btn-icon"><i class="fa fa-trash"></i></button>';
						echo '</td>';
						echo '</tr>';

						$usd_total_amount += $order['amount'];
						$usd_total_vat += $order['vat'];
						$usd_total_total += $order['total'];
						$usd_total_net += $order['net'];
						$usd_total_usd += $order['usd'];
						$usd_counter++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="table-warning">
						<th class="text-center" colspan="2">รายการ <?php echo $usd_counter; ?> รายการ (USD)</th>
						<th class="text-center"><?php echo number_format($usd_total_amount, 4); ?></th>
						<th class="text-center"></th>
						<th class="text-center hide-mobile"><?php echo number_format($usd_total_vat, 2); ?></th>
						<th class="text-center hide-mobile"><?php echo number_format($usd_total_total, 2); ?></th>
						<th class="text-center"><?php echo number_format($usd_total_net, 2); ?></th>
						<th class="text-center"><?php echo number_format($usd_total_usd, 2); ?></th>
						<th class="text-center" colspan="5"></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
<?php } ?>

<div class="summary-cards">
	<div class="row">
		<div class="col-md-4 col-12">
			<div class="card">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0">สรุปยอดขาย THB</h5>
				</div>
				<div class="card-body">
					<p><strong>จำนวนรายการ:</strong> <?php echo $thb_counter; ?> รายการ</p>
					<p><strong>ยอดรวมกิโล:</strong> <?php echo number_format($thb_total_amount, 4); ?> กิโล</p>
					<p><strong>ยอดรวม:</strong> <?php echo number_format($thb_total_total, 2); ?> บาท</p>
					<p><strong>ยอดรวม + VAT:</strong> <?php echo number_format($thb_total_net, 2); ?> บาท</p>
				</div>
			</div>
		</div>
		<?php if (count($usd_orders) > 0) { ?>
			<div class="col-md-4 col-12">
				<div class="card">
					<div class="card-header bg-warning text-dark">
						<h5 class="mb-0">สรุปยอดขาย USD</h5>
					</div>
					<div class="card-body">
						<p><strong>จำนวนรายการ:</strong> <?php echo $usd_counter; ?> รายการ</p>
						<p><strong>ยอดรวมกิโล:</strong> <?php echo number_format($usd_total_amount, 4); ?> กิโล</p>
						<p><strong>ยอดรวม:</strong> <?php echo number_format($usd_total_total, 2); ?> บาท</p>
						<p><strong>ยอดรวม + VAT:</strong> <?php echo number_format($usd_total_net, 2); ?> บาท</p>
						<p><strong>ยอดรวม USD:</strong> $<?php echo number_format($usd_total_usd, 2); ?></p>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="col-md-4 col-12">
			<div class="card">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0">สรุปยอดรวมทั้งหมด</h5>
				</div>
				<div class="card-body">
					<p><strong>จำนวนรายการ:</strong> <?php echo ($thb_counter + $usd_counter); ?> รายการ</p>
					<p><strong>ยอดรวมกิโล:</strong> <?php echo number_format(($thb_total_amount + $usd_total_amount), 4); ?> กิโล</p>
					<p><strong>ยอดรวม THB:</strong> <?php echo number_format(($thb_total_total + $usd_total_total), 2); ?> บาท</p>
					<p><strong>ยอดรวม + VAT:</strong> <?php echo number_format(($thb_total_net + $usd_total_net), 2); ?> บาท</p>
					<?php if (count($usd_orders) > 0) { ?>
						<p><strong>ยอดรวม USD:</strong> $<?php echo number_format($usd_total_usd, 2); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
// ======================= SECTION PER STORE =======================
// กำหนดสโตร์ที่จะโชว์และชื่อหัวเรื่อง
$stores = [
	'BWS' => 'Bowins Silver',
	'SILVERNOW' => 'SILVER NOW',
	'LG' => 'Luck Gems',
	'EXHIBITION' => 'Exhibition'
];

// ฟังก์ชันช่วยเรนเดอร์ตาราง (ลดการเขียนซ้ำ)
function render_orders_table($dbc, $orders, $date, $is_usd = false, $title_prefix = '', $table_theme = 'dark')
{
	if (count($orders) === 0) {
		return;
	}

	$thead_class = $is_usd ? 'table-warning' : 'table-dark';
	$caption = $is_usd ? '(USD)' : '(THB)';

	// สะสมยอด
	$total_amount = 0;
	$total_vat = 0;
	$total_total = 0;
	$total_net = 0;
	$total_usd = 0;
	$counter = 0;

	echo '<div class="sales-table-responsive">';
	echo '<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">';
	echo '<thead>';
	echo '<tr>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold" colspan="' . ($is_usd ? '13' : '12') . '">'
		. htmlspecialchars($title_prefix) . ' ' . $caption . ' ประจำวันที่ ' . date("d/m/Y", strtotime($date)) .
		'</th>';
	echo '</tr>';

	echo '<tr>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold">PO</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold">CUSTOMER</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold">KIO</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold">BATH/KGS</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold hide-mobile">VAT</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold hide-mobile">TOTAL</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold">TOTAL+VAT</th>';
	if ($is_usd) {
		echo '<th class="text-center ' . $thead_class . ' font-weight-bold">USD</th>';
	}
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold hide-mobile">DELIVERY</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold">SALE</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold hide-mobile">SP</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold hide-mobile">EX</th>';
	echo '<th class="text-center ' . $thead_class . ' font-weight-bold">DEL</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';

	foreach ($orders as $order) {
		$bgsuccess = ($order['product_id'] == '4') ? 'class="bg-success text-white font-weight-bold"' : '';
		echo '<tr ' . $bgsuccess . '>';
		echo '<td class="text-left">' . $order['code'] . '</td>';
		echo '<td class="text-left">' . $order['customer_name'] . '</td>';
		echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
		echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
		echo '<td class="text-right hide-mobile">' . number_format($order['vat'], 2) . '</td>';
		echo '<td class="text-right hide-mobile">' . number_format($order['total'], 2) . '</td>';
		echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
		if ($is_usd) {
			echo '<td class="text-right">' . number_format($order['usd'], 2) . '</td>';
		}
		echo '<td class="text-center hide-mobile">';
		if ($order['delivery_date'] == null) {
			echo '<span class="badge badge-danger">lock</span>';
		} else {
			echo date("d/m/y", strtotime($order['delivery_date']));
		}
		echo '</td>';
		echo '<td class="text-left">';
		if ($order['sales'] != "") {
			$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
			echo $employee['fullname'];
		} else {
			echo "-";
		}
		echo '</td>';
		echo '<td class="text-right hide-mobile">' . number_format($order['rate_spot'], 2) . '</td>';
		echo '<td class="text-right hide-mobile">' . number_format($order['rate_exchange'], 2) . '</td>';
		echo '<td class="text-center">';
		echo '<button onclick="fn.app.sales.order.dialog_remove_each(' . $order['id'] . ')" class="btn btn-xs btn-outline-danger mr-1 btn-icon"><i class="fa fa-trash"></i></button>';
		echo '</td>';
		echo '</tr>';

		$total_amount += $order['amount'];
		$total_vat += $order['vat'];
		$total_total += $order['total'];
		$total_net += $order['net'];
		if ($is_usd) {
			$total_usd += $order['usd'];
		}
		$counter++;
	}

	echo '</tbody>';
	echo '<tfoot>';
	echo '<tr class="' . ($is_usd ? 'table-warning' : 'table-warning') . '">';
	echo '<th class="text-center" colspan="2">รายการ ' . $counter . ' รายการ ' . ($is_usd ? '(USD)' : '(THB)') . '</th>';
	echo '<th class="text-center">' . number_format($total_amount, 4) . '</th>';
	echo '<th class="text-center"></th>';
	echo '<th class="text-center hide-mobile">' . number_format($total_vat, 2) . '</th>';
	echo '<th class="text-center hide-mobile">' . number_format($total_total, 2) . '</th>';
	echo '<th class="text-center">' . number_format($total_net, 2) . '</th>';
	if ($is_usd) {
		echo '<th class="text-center">' . number_format($total_usd, 2) . '</th>';
	}
	echo '<th class="text-center" colspan="' . ($is_usd ? '5' : '5') . '"></th>';
	echo '</tr>';
	echo '</tfoot>';
	echo '</table>';
	echo '</div>';

	// การ์ดสรุปย่อย
	echo '<div class="summary-cards">';
	echo '  <div class="row">';
	echo '    <div class="col-md-4 col-12">';
	echo '      <div class="card">';
	echo '        <div class="card-header ' . ($is_usd ? 'bg-warning text-dark' : 'bg-primary text-white') . '">';
	echo '          <h5 class="mb-0">' . htmlspecialchars($title_prefix) . ' - สรุปยอด ' . ($is_usd ? 'USD' : 'THB') . '</h5>';
	echo '        </div>';
	echo '        <div class="card-body">';
	echo '          <p><strong>จำนวนรายการ:</strong> ' . $counter . ' รายการ</p>';
	echo '          <p><strong>ยอดรวมกิโล:</strong> ' . number_format($total_amount, 4) . ' กิโล</p>';
	echo '          <p><strong>ยอดรวม:</strong> ' . number_format($total_total, 2) . ' บาท</p>';
	echo '          <p><strong>ยอดรวม + VAT:</strong> ' . number_format($total_net, 2) . ' บาท</p>';
	if ($is_usd) {
		echo '      <p><strong>ยอดรวม USD:</strong> $' . number_format($total_usd, 2) . '</p>';
	}
	echo '        </div>';
	echo '      </div>';
	echo '    </div>';
	echo '  </div>';
	echo '</div>';
}

// วนทำทีละสโตร์
foreach ($stores as $storeCode => $storeName) {

	$sql_store = "SELECT * FROM bs_orders 
		WHERE DATE(date) LIKE '" . $date . "' 
		  AND bs_orders.status > 0 
		  AND bs_orders.store = '" . $dbc->Escape_String($storeCode) . "'";
	$rst_store = $dbc->Query($sql_store);

	$store_thb = [];
	$store_usd = [];
	while ($o = $dbc->Fetch($rst_store)) {
		if ($o['usd'] > 0) {
			$store_usd[] = $o;
		} else {
			$store_thb[] = $o;
		}
	}

	// ถ้าไม่มีข้อมูลของสโตร์นั้นเลย ให้ข้าม
	if (count($store_thb) === 0 && count($store_usd) === 0) {
		continue;
	}

	// หัวข้อสโตร์
	echo '<div class="section-heading">' . htmlspecialchars($storeName) . '</div>';

	// ตาราง THB ของสโตร์ (ถ้ามี)
	if (count($store_thb) > 0) {
		render_orders_table($dbc, $store_thb, $date, false, $storeName);
	}

	// ตาราง USD ของสโตร์ (ถ้ามี)
	if (count($store_usd) > 0) {
		echo '<div class="mt-3">';
		render_orders_table($dbc, $store_usd, $date, true, $storeName);
		echo '</div>';
	}
}
?>

<script>
	$(document).ready(function() {
		$('.sales-table-responsive').on('scroll', function() {
			if ($(this).scrollLeft() > 10) {
				$(this).addClass('scrolled');
			} else {
				$(this).removeClass('scrolled');
			}
		});
	});
</script>