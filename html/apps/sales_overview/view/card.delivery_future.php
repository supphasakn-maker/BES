<?php
$date = date('Y-m-d');
$datetomorrow = date('Y-m-d', strtotime($date . "+1 days"));

$data_group = [];
$data_lock = [];
$data_deposit = [];

$sql = "
    SELECT 
        d.delivery_date,
        o.product_id,
        SUM(o.amount) AS amount,
        SUM(o.amount * o.price) AS total,
        SUM(o.amount * o.price * 1.07) AS net
    FROM bs_deliveries d
    LEFT JOIN bs_orders o ON o.delivery_id = d.id
    WHERE o.status > 0 AND d.delivery_date >= '$datetomorrow'
    GROUP BY d.delivery_date, o.product_id
";
$rst = $dbc->Query($sql);
while ($row = $dbc->Fetch($rst)) {
	$delivery = $row['delivery_date'];
	$type = ($row['product_id'] == 2) ? 'แท่งเงิน' : 'เม็ดเงิน';

	$data_group[$delivery][$type]['amount'] = ($data_group[$delivery][$type]['amount'] ?? 0) + (float)$row['amount'];
	$data_group[$delivery][$type]['total'] = ($data_group[$delivery][$type]['total'] ?? 0) + (float)$row['total'];
	$data_group[$delivery][$type]['net'] = ($data_group[$delivery][$type]['net'] ?? 0) + (float)$row['net'];
}

// เรียงลำดับตามวันที่
ksort($data_group);

$sql = "
    SELECT 
        product_id,
        SUM(amount) AS amount,
        SUM(amount * price) AS total,
        SUM(amount * price * 1.07) AS net
    FROM bs_orders
    WHERE delivery_date IS NULL AND date > '2021-12-01' AND status > 0 AND keep_silver != 1
    GROUP BY product_id
";
$rst = $dbc->Query($sql);
while ($row = $dbc->Fetch($rst)) {
	$type = ($row['product_id'] == 2) ? 'แท่งเงิน' : 'เม็ดเงิน';
	$data_lock[$type] = [
		'amount' => (float)$row['amount'],
		'total' => (float)$row['total'],
		'net' => (float)$row['net'],
	];
}

$sql = "
    SELECT 
        product_id,
        SUM(amount) AS amount,
        SUM(amount * price) AS total,
        SUM(amount * price * 1.07) AS net
    FROM bs_orders
    WHERE delivery_date IS NULL AND date > '2021-12-01' AND status > 0 AND keep_silver = 1
    GROUP BY product_id
";
$rst = $dbc->Query($sql);
while ($row = $dbc->Fetch($rst)) {
	$type = ($row['product_id'] == 2) ? 'แท่งเงิน' : 'เม็ดเงิน';
	$data_deposit[$type] = [
		'amount' => (float)$row['amount'],
		'total' => (float)$row['total'],
		'net' => (float)$row['net'],
	];
}

function sum_all($sources)
{
	$sum = ['amount' => 0, 'total' => 0, 'net' => 0];
	foreach ($sources as $source) {
		foreach ($source as $type => $val) {
			$sum['amount'] += $val['amount'] ?? 0;
			$sum['total'] += $val['total'] ?? 0;
			$sum['net'] += $val['net'] ?? 0;
		}
	}
	return $sum;
}

$all_sum = sum_all([$data_lock, $data_deposit]);

foreach ($data_group as $types) {
	foreach ($types as $val) {
		foreach ($val as $k => $v) {
			$all_sum[$k] += $v;
		}
	}
}

echo '<style>
    table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: right; }
    th { background: #f2f2f2; }
    td.label { text-align: left; font-weight: bold; }
    h5 { margin-top: 10px; }
</style>';

$total_bar_amount = 0;
$total_bar_total = 0;
$total_bar_net = 0;

$total_grain_amount = 0;
$total_grain_total = 0;
$total_grain_net = 0;

foreach ($data_group as $date => $types) {
	foreach ($types as $type => $values) {
		if ($type == 'แท่งเงิน') {
			$total_bar_amount += $values['amount'] ?? 0;
			$total_bar_total += $values['total'] ?? 0;
			$total_bar_net += $values['net'] ?? 0;
		} elseif ($type == 'เม็ดเงิน') {
			$total_grain_amount += $values['amount'] ?? 0;
			$total_grain_total += $values['total'] ?? 0;
			$total_grain_net += $values['net'] ?? 0;
		}
	}
}

echo "<h5>Delivery Future</h5>";
echo "<table>";
echo "<thead>";
echo "<tr><th>วันที่</th><th>ประเภท</th><th>Amount (kg)</th><th>Total (฿)</th><th>Net (฿)</th></tr>";
echo "</thead>";
echo "<tbody>";
foreach ($data_group as $date => $types) {
	$rowspan = count($types); 
	$i = 0;
	foreach ($types as $type => $val) {
		echo "<tr>";
		if ($i === 0) {
			echo "<td class='label' rowspan='$rowspan'>" . $date . "</td>";
		}
		echo "<td class='label'>" . $type . "</td>";
		echo "<td align='right'>" . number_format($val['amount'], 2) . "</td>";
		echo "<td align='right'>" . number_format($val['total'], 2) . "</td>";
		echo "<td align='right'>" . number_format($val['net'], 2) . "</td>";
		echo "</tr>";
		$i++;
	}
}
echo "</tbody>";
echo "<tfoot style='font-weight:bold; background:#eee;'>";
echo "<tr>";
echo "<td colspan='2' align='right'>รวมแท่งเงิน</td>";
echo "<td align='right'>" . number_format($total_bar_amount, 2) . "</td>";
echo "<td align='right'>" . number_format($total_bar_total, 2) . "</td>";
echo "<td align='right'>" . number_format($total_bar_net, 2) . "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan='2' align='right'>รวมเม็ดเงิน</td>";
echo "<td align='right'>" . number_format($total_grain_amount, 2) . "</td>";
echo "<td align='right'>" . number_format($total_grain_total, 2) . "</td>";
echo "<td align='right'>" . number_format($total_grain_net, 2) . "</td>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";

echo "<h5>Lock</h5>";
echo "<table>";
echo "<tr><th>ประเภท</th><th>Amount (kg)</th><th>Total (฿)</th><th>Net (฿)</th></tr>";
foreach ($data_lock as $type => $val) {
	echo "<tr>";
	echo "<td class='label'>$type</td>";
	echo "<td>" . number_format($val['amount'], 2) . "</td>";
	echo "<td>" . number_format($val['total'], 2) . "</td>";
	echo "<td>" . number_format($val['net'], 2) . "</td>";
	echo "</tr>";
}
echo "</table>";

echo "<h5>ฝาก</h5>";
echo "<table>";
echo "<tr><th>ประเภท</th><th>Amount (kg)</th><th>Total (฿)</th><th>Net (฿)</th></tr>";
foreach ($data_deposit as $type => $val) {
	echo "<tr>";
	echo "<td class='label'>$type</td>";
	echo "<td>" . number_format($val['amount'], 2) . "</td>";
	echo "<td>" . number_format($val['total'], 2) . "</td>";
	echo "<td>" . number_format($val['net'], 2) . "</td>";
	echo "</tr>";
}
echo "</table>";

echo "<h5>รวมทั้งหมด</h5>";
echo "<table>";
echo "<tr><th class='label'>รวม</th><th>Amount (kg)</th><th>Total (฿)</th><th>Net (฿)</th></tr>";
echo "<tr>";
echo "<td class='label'>ทั้งหมด</td>";
echo "<td>" . number_format($all_sum['amount'], 2) . "</td>";
echo "<td>" . number_format($all_sum['total'], 2) . "</td>";
echo "<td>" . number_format($all_sum['net'], 2) . "</td>";
echo "</tr>";
echo "</table>";
?>