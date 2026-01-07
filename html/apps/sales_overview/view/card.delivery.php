<style>
	.table-responsive-custom {
		overflow-x: auto;
		-webkit-overflow-scrolling: touch;
		margin-bottom: 1rem;
	}

	.table-sm td,
	.table-sm th {
		padding: 0.3rem;
		font-size: 0.9rem;
	}

	@media (min-width: 768px) and (max-width: 1024px) {

		.table-sm td,
		.table-sm th {
			font-size: 0.9rem;
			padding: 0.25rem;
		}

		.table-sm .text-right,
		.table-sm .text-center {
			white-space: nowrap;
		}
	}

	@media (max-width: 767px) {
		.table-sm {
			font-size: 0.75rem;
		}

		.table-sm td,
		.table-sm th {
			padding: 0.2rem;
			font-size: 0.7rem;
		}

		.table-sm th {
			font-size: 0.65rem;
		}

		.table-sm .hide-mobile {
			display: none;
		}
	}

	.table-responsive-custom::after {
		content: '← เลื่อนดู →';
		position: absolute;
		bottom: 0;
		right: 0;
		background: rgba(0, 32, 78, 0.8);
		color: white;
		padding: 0.25rem 0.5rem;
		font-size: 0.7rem;
		border-radius: 3px 0 0 0;
		opacity: 0;
		transition: opacity 0.3s;
	}

	@media (max-width: 1024px) {
		.table-responsive-custom::after {
			opacity: 1;
		}

		.table-responsive-custom.scrolled::after {
			opacity: 0;
		}
	}
</style>

<div class="mb-1">
	<a href="index.php#apps/sales_overview/index.php?view=delivery<?php if (isset($_GET['date'])) echo "&date=" . $_GET['date']; ?>" class="btn btn-xs btn-outline-dark">View</a>
</div>

<div class="table-responsive-custom">
	<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
		<thead>
			<tr>
				<th class="text-center table-dark font-weight-bold" colspan="10">ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?></th>
			</tr>
			<tr>
				<th class="text-center table-dark font-weight-bold">DATE</th>
				<th class="text-center table-dark font-weight-bold">BILL NO.</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">ORDER NO.</th>
				<th class="text-center table-dark font-weight-bold">CUSTOMER</th>
				<th class="text-center table-dark font-weight-bold">KIO</th>
				<th class="text-center table-dark font-weight-bold">BATH/KGS.</th>
				<th class="text-center table-dark font-weight-bold">TOTAL+VAT</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">PERIOD</th>
				<th class="text-center table-dark font-weight-bold hide-mobile">TYPE</th>
				<th class="text-center table-dark font-weight-bold">SALES</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$total_amount = 0;
			$total_net = 0;
			$sql = "SELECT 
					bs_orders.date AS date,
					bs_deliveries.delivery_date AS delivery_date,
					bs_deliveries.billing_id AS code,
					bs_deliveries.id AS id,
					bs_orders.customer_name AS customer_name,
					bs_orders.amount AS amount,
					bs_orders.price AS price,
					bs_orders.total AS total,
					bs_orders.vat AS vat,
					bs_orders.net AS net,
					bs_orders.code AS order_number,
					bs_orders.sales AS sales,
					bs_orders.orderable_type AS orderable_type,
					bs_orders.delivery_time AS delivery_time
				FROM bs_deliveries
				LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
				WHERE
					DATE(bs_deliveries.delivery_date) = '" . $date . "'
					AND bs_orders.status > 0
				ORDER BY bs_orders.delivery_date ASC";

			$rst = $dbc->Query($sql);
			while ($order = $dbc->Fetch($rst)) {

				$type_th = "";
				switch ($order['orderable_type']) {
					case "delivered_by_company":
						$type_th = "จัดส่งโดยรถบริษัท";
						break;
					case "post_office":
						$type_th = "จัดส่งโดยไปรษณีย์ไทย";
						break;
					case "receive_at_company":
						$type_th = "รับสินค้าที่บริษัท";
						break;
					case "receive_at_luckgems":
						$type_th = "รับสินค้าที่ Luck Gems";
						break;
					default:
						$type_th = "-";
				}
				echo '<tr>';
				echo '<td class="text-nowrap">' . date("d/m/Y", strtotime($order['date'])) . '</td>';
				echo '<td class="text-nowrap">' . $order['code'] . '</td>';
				echo '<td class="hide-mobile">' . $order['order_number'] . '</td>';
				echo '<td class="text-left">' . $order['customer_name'] . '</td>';
				echo '<td class="text-right text-nowrap">' . number_format($order['amount'], 4) . '</td>';
				echo '<td class="text-right text-nowrap">' . number_format($order['price'], 2) . '</td>';
				echo '<td class="text-right text-nowrap">' . number_format($order['net'], 2) . '</td>';
				echo '<td class="hide-mobile">' . $order['delivery_time'] . '</td>';
				echo '<td class="hide-mobile font-weight-bold">' . $type_th . '</td>';
				echo '<td class="text-center">';
				if ($order['sales'] != "") {
					$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
					echo $employee['fullname'];
				} else {
					echo "-";
				}
				echo '</td>';
				echo '</tr>';
				$total_amount += $order['amount'];
				$total_net += $order['net'];
			}
			?>
		</tbody>
		<tfoot>
			<tr class="table-warning">
				<th colspan="4" class="text-right">รวม</th>
				<th class="text-right"><?php echo number_format($total_amount, 4); ?></th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_net, 2); ?></th>
				<th colspan="3"></th>
			</tr>
		</tfoot>
	</table>
</div>

<?php
$total_amount = 0;
$summary_data = array();

$sql_summary = "SELECT bdi.name, SUM(bdi.amount) AS total_amount
FROM bs_deliveries bd
LEFT JOIN bs_orders bo ON bo.delivery_id = bd.id
LEFT JOIN bs_delivery_items bdi ON bdi.delivery_id = bd.id
WHERE DATE(bd.delivery_date) = '$date' AND bo.status > 0
GROUP BY bdi.name
ORDER BY bdi.name DESC";

$rst_summary = $dbc->Query($sql_summary);
while ($row_summary = $dbc->Fetch($rst_summary)) {
	$summary_data[] = $row_summary;
	$total_amount += $row_summary['total_amount'];
}

$grand_total = $total_amount;
?>

<div class="card mt-3">
	<div class="card-header">
		<h5>รายการส่งของวันที่ <?php echo date('d/m/Y', strtotime($date)); ?></h5>
	</div>
	<div class="card-body">
		<div class="table-responsive-custom">
			<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
				<thead>
					<tr>
						<th class="font-weight-bold">ประเภทถุง</th>
						<th class="text-center font-weight-bold">จำนวน</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($summary_data)) {
						foreach ($summary_data as $item) {
							$unit = (stripos($item['name'], 'SILVER BAR 1 KG') !== false) ? 'แท่ง' : 'ถุง';

							echo '<tr>';
							echo '<td>' . htmlspecialchars($item['name']) . '</td>';
							echo '<td class="text-center"><strong>' . number_format($item['total_amount'], 0) . ' ' . $unit . '</strong></td>';
							echo '</tr>';
						}
					} else {
						echo '<tr><td colspan="2" class="text-center text-muted">ไม่มีข้อมูล</td></tr>';
					}
					?>
				</tbody>
				<tfoot>
					<tr class="table-warning">
						<th>รวมทั้งหมด</th>
						<th class="text-center"><?php echo number_format($total_amount, 0); ?> รายการ</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('.table-responsive-custom').on('scroll', function() {
			if ($(this).scrollLeft() > 10) {
				$(this).addClass('scrolled');
			} else {
				$(this).removeClass('scrolled');
			}
		});
	});
</script>