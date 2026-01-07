<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

@ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set(DEFAULT_TIMEZONE);

// บังคับ JSON สะอาด
if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}
if (ob_get_level() === 0) {
    ob_start();
}

// แปลง fatal เป็น JSON เสมอ
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(500);
        echo json_encode([
            'error' => 'fatal',
            'message' => $err['message'],
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'draw' => intval($_GET['draw'] ?? 1)
        ], JSON_UNESCAPED_UNICODE);
    }
});

// ฟังก์ชัน normalize วันที่ → 'YYYY-MM-DD' หรือ '' (ไม่คืน NULL)
function normalize_date_str($v)
{
    if (!$v) return '';
    $v = trim((string)$v);
    if ($v === '' || $v === '0000-00-00' || $v === '0000-00-00 00:00:00') return '';
    if (strpos($v, ' ') !== false) {
        $v = explode(' ', $v)[0];
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return '';
    return $v;
}

try {
    $dbc = new dbc;
    $dbc->Connect();

    // DataTables params
    $start = intval($_GET['start'] ?? 0);
    $length = intval($_GET['length'] ?? 25);
    $search_value = $_GET['search']['value'] ?? '';

    $where_conditions = [];

    // filter by date range (delivery_date)
    if (!empty($_GET['date_from'])) {
        $date_from = addslashes($_GET['date_from']);
        $date_to   = addslashes($_GET['date_to'] ?? $_GET['date_from']);
        // หมายเหตุ: เงื่อนไขนี้เปิดให้ทั้ง NULL และช่วงวัน
        $where_conditions[] = "((parent.delivery_date BETWEEN '$date_from' AND '$date_to') OR parent.delivery_date IS NULL)";
    }

    // specific delivery date
    if (!empty($_GET['delivery_date'])) {
        $delivery_date = addslashes($_GET['delivery_date']);
        $where_conditions[] = "parent.delivery_date = '$delivery_date'";
    }

    // customer
    if (!empty($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        $where_conditions[] = "parent.customer_id = $customer_id";
    }

    // combine mode: แสดงเฉพาะที่ยังไม่มี delivery_id
    if (isset($_GET['combine_mode']) && $_GET['combine_mode'] === '1') {
        $where_conditions[] = "parent.delivery_id IS NULL";
    }

    // search
    if (!empty($search_value)) {
        $q = addslashes($search_value);
        $where_conditions[] = "("
            . "parent.code LIKE '%$q%'"
            . " OR parent.customer_name LIKE '%$q%'"
            . " OR parent.phone LIKE '%$q%'"
            . " OR bs_customers_bwd.username LIKE '%$q%'"
            . " OR parent.platform LIKE '%$q%')";
    }

    $where_clause = !empty($where_conditions) ? 'AND ' . implode(' AND ', $where_conditions) : '';

    // count
    $count_sql = "
        SELECT COUNT(DISTINCT parent.id) as total
        FROM bs_orders_bwd parent
        LEFT JOIN bs_customers_bwd ON parent.customer_id = bs_customers_bwd.id
        WHERE parent.parent IS NULL $where_clause
    ";
    $count_result = $dbc->Query($count_sql);
    $count_row = $dbc->Fetch($count_result);
    $total_records = $count_row ? intval($count_row['total']) : 0;
    $filtered_records = $total_records;

    // main query: ดึง delivery_date ทั้งจาก order และ deliveries
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
            parent.discount_type,

            parent.delivery_date            AS order_delivery_date,
            bs_deliveries_bwd.delivery_date AS delivery_delivery_date,

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

            FORMAT(child_sum.total_amount, 4) as amount,
            FORMAT(child_sum.total_price,  2) as price,
            FORMAT(child_sum.total_discount,2) as discount,
            FORMAT(child_sum.total_net,    2) as net,
            FORMAT(child_sum.total_total,  2) as total,
            child_sum.item_count as item_count,

            COALESCE(os_users.display, '')          as sales,
            COALESCE(bs_deliveries_bwd.code, '')    as delivery_code,
            COALESCE(bs_deliveries_bwd.code, '')    as order_code,
            COALESCE(bs_customers_bwd.username, '') as username,
            COALESCE(bs_deliveries_bwd.payment_note, '') as payment_note,
            COALESCE(bs_deliveries_bwd.billing_id, '')   as billing_id
        FROM bs_orders_bwd parent
        LEFT JOIN (
            SELECT 
                COALESCE(parent, id) as parent_id,
                COUNT(*)  as item_count,
                SUM(amount)   as total_amount,
                SUM(price)    as total_price,
                SUM(discount) as total_discount,
                SUM(net)      as total_net,
                SUM(total)    as total_total
            FROM bs_orders_bwd 
            WHERE status > 0
            GROUP BY COALESCE(parent, id)
        ) child_sum ON child_sum.parent_id = parent.id
        LEFT JOIN os_users          ON parent.user = os_users.id
        LEFT JOIN bs_deliveries_bwd ON parent.delivery_id = bs_deliveries_bwd.id
        LEFT JOIN bs_customers_bwd  ON parent.customer_id = bs_customers_bwd.id
        WHERE parent.parent IS NULL $where_clause
        ORDER BY parent.date ASC
        LIMIT $start, $length
    ";

    $result = $dbc->Query($sql);

    $data = [];
    if ($result) {
        while ($row = $dbc->Fetch($result)) {
            $row['DT_RowId'] = 'row_' . $row['id'];

            foreach ($row as $k => $v) {
                if ($v === null) $row[$k] = '';
            }

            // date (DATETIME) → YYYY-MM-DD
            $row['date'] = normalize_date_str($row['date']);

            // delivery_date: ใช้ order ก่อน ถ้าว่างใช้ deliveries, ถ้ายังว่าง → ''
            $d1 = normalize_date_str($row['order_delivery_date']);
            $d2 = normalize_date_str($row['delivery_delivery_date']);
            $row['delivery_date'] = $d1 !== '' ? $d1 : $d2;

            unset($row['order_delivery_date'], $row['delivery_delivery_date']);

            $data[] = $row;
        }
    }

    // ล้าง buffer อื่น ๆ ให้เหลือแต่ JSON
    while (ob_get_level() > 1) {
        ob_end_clean();
    }

    echo json_encode([
        'draw' => intval($_GET['draw'] ?? 1),
        'recordsTotal' => $total_records,
        'recordsFiltered' => $filtered_records,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Exception $e) {
    while (ob_get_level() > 1) {
        ob_end_clean();
    }
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage(),
        'data' => [],
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'draw' => intval($_GET['draw'] ?? 1)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
