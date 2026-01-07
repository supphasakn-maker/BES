<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

try {
	$dbc = new dbc;
	$dbc->Connect();

	$start = intval($_GET['start'] ?? 0);
	$length = intval($_GET['length'] ?? 25);
	$search_value = $_GET['search']['value'] ?? '';

	$where_conditions = [];

	if (!empty($_GET['date_from'])) {
		$date_from = addslashes($_GET['date_from']);
		$date_to = addslashes($_GET['date_to'] ?? $_GET['date_from']);
		$where_conditions[] = "(delivery.delivery_date BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59')";
	}

	if (!empty($search_value)) {
		$search_value = addslashes($search_value);
		$where_conditions[] = "(parent.code LIKE '%$search_value%' OR parent.customer_name LIKE '%$search_value%' OR delivery.code LIKE '%$search_value%')";
	}

	$where_clause = !empty($where_conditions) ? 'AND ' . implode(' AND ', $where_conditions) : '';

	// นับจำนวนทั้งหมด
	$count_sql = "
		SELECT COUNT(DISTINCT parent.id) as total
		FROM bs_orders_bwd parent
		LEFT JOIN bs_orders_bwd o ON (o.parent = parent.id OR o.id = parent.id)
		LEFT JOIN bs_deliveries_bwd delivery ON parent.delivery_id = delivery.id
		WHERE parent.parent IS NULL AND o.status > 0 $where_clause
	";
	$count_result = $dbc->Query($count_sql);
	$total_records = $dbc->Fetch($count_result)['total'];

	// Main Query
	$sql = "
		SELECT 
			parent.id,
			parent.code AS code,
			delivery.code AS order_code,
			parent.customer_name,
			parent.date,
			parent.user,
			parent.info_payment,
			parent.billing_id,
			delivery.payment_note,
			delivery.code AS delivery_code,
			delivery.delivery_date,
			delivery.type,
			delivery.status,
			delivery.comment,
			FORMAT(SUM(o.amount), 4) AS amount,
			FORMAT(SUM(o.price), 2) AS price,
			FORMAT(SUM(o.net), 2) AS net
		FROM bs_orders_bwd parent
		LEFT JOIN bs_orders_bwd o ON (o.parent = parent.id OR o.id = parent.id)
		LEFT JOIN bs_deliveries_bwd delivery ON parent.delivery_id = delivery.id
		WHERE parent.parent IS NULL AND o.status > 0 $where_clause
		GROUP BY parent.id
		ORDER BY parent.id DESC
		LIMIT $start, $length
	";

	$result = $dbc->Query($sql);
	$data = [];
	while ($row = $dbc->Fetch($result)) {
		$data[] = $row;
	}

	$response = [
		'draw' => intval($_GET['draw'] ?? 1),
		'recordsTotal' => $total_records,
		'recordsFiltered' => $total_records,
		'data' => $data
	];

	echo json_encode($response);
} catch (Exception $e) {
	echo json_encode([
		'error' => $e->getMessage(),
		'data' => [],
		'recordsTotal' => 0,
		'recordsFiltered' => 0
	]);
	error_log("Delivery Order Query Error: " . $e->getMessage());
} finally {
	if (isset($dbc)) {
		$dbc->Close();
	}
}
