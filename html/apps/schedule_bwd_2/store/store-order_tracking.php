<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

try {
    $dbc = new dbc;
    $dbc->Connect();

    // Parameters from DataTables
    $start = intval($_GET['start'] ?? 0);
    $length = intval($_GET['length'] ?? 25);
    $search_value = $_GET['search']['value'] ?? '';

    // Build WHERE conditions
    $where_conditions = [];

    if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
        $date_from = addslashes($_GET['date_from']);
        $date_to = addslashes($_GET['date_to'] ?? $_GET['date_from']);
        $where_conditions[] = "((parent.delivery_date BETWEEN '$date_from' AND '$date_to') OR parent.delivery_date IS NULL  AND parent.platform IN('Exhibition','Facebook', 'IG', 'Website', 'LINE','SilverNow'))";
    }

    if (isset($_GET['delivery_date']) && !empty($_GET['delivery_date'])) {
        $delivery_date = addslashes($_GET['delivery_date']);
        $where_conditions[] = "parent.delivery_date = '$delivery_date'";
    }

    if (isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        $where_conditions[] = "parent.customer_id = $customer_id";
    }

    if (isset($_GET['combine_mode'])) {
        $where_conditions[] = "parent.delivery_id IS NULL";
    }

    // Search condition
    if (!empty($search_value)) {
        $search_value = addslashes($search_value);
        $where_conditions[] = "(parent.code LIKE '%$search_value%' OR parent.customer_name LIKE '%$search_value%' OR parent.phone LIKE '%$search_value%')";
    }

    $where_clause = !empty($where_conditions) ? 'AND ' . implode(' AND ', $where_conditions) : '';

    // Count total records
    $count_sql = "
        SELECT COUNT(DISTINCT parent.id) as total
        FROM bs_orders_bwd parent
        LEFT JOIN bs_orders_bwd o ON (o.id = parent.id OR o.parent = parent.id)
        WHERE parent.parent IS NULL AND o.status > 0 $where_clause
    ";

    $count_result = $dbc->Query($count_sql);
    $total_records = $dbc->Fetch($count_result)['total'];

    // Main query
    $sql = "
        SELECT 
            parent.id,
            parent.customer_id,
            parent.code,
            parent.customer_name,
            parent.phone,
            parent.platform,
            parent.date,
            parent.user,
            parent.type,
            parent.parent,
            parent.created,
            parent.updated,
            FORMAT(SUM(o.amount), 4) as amount,
            FORMAT(SUM(o.price), 2) as price,
            parent.discount_type,
            FORMAT(SUM(o.discount), 2) as discount,
            FORMAT(SUM(o.net), 2) as net,
            FORMAT(SUM(o.total), 2) as total,
            parent.delivery_date,
            parent.delivery_time,
            parent.lock_status,
            parent.status,
            parent.shipping_address,
            parent.billing_address,
            parent.shipping,
            parent.engrave,
            parent.font,
            parent.carving,
            parent.billing_id,
            parent.default_bank,
            parent.info_payment,
            parent.info_contact,
            parent.delivery_id,
            parent.remove_reason,
            parent.product_type,
            parent.product_id,
            parent.Tracking,
            COUNT(o.id) as item_count,
            os_users.display as sales,
            bs_deliveries_bwd.code as delivery_code
        FROM bs_orders_bwd parent
        LEFT JOIN bs_orders_bwd o ON (o.id = parent.id OR o.parent = parent.id)
        LEFT JOIN os_users ON parent.user = os_users.id
        LEFT JOIN bs_deliveries_bwd ON parent.delivery_id = bs_deliveries_bwd.id
        WHERE parent.parent IS NULL AND o.status > 0 $where_clause  AND parent.platform IN('Exhibition','Facebook', 'IG', 'Website', 'LINE','SilverNow')
        GROUP BY parent.id
        ORDER BY parent.id DESC
        LIMIT $start, $length
    ";

    $result = $dbc->Query($sql);

    // Fetch all results
    $data = [];
    while ($row = $dbc->Fetch($result)) {
        // ใช้ associative array แทน array_values
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
    error_log("Custom SQL error: " . $e->getMessage());
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
