<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

try {
    $dbc = new dbc;
    $dbc->Connect();

    // DataTables params
    $start        = intval($_GET['start']  ?? 0);
    $length       = intval($_GET['length'] ?? 25);
    $search_value = $_GET['search']['value'] ?? '';

    $where = [];

    // --- ไม่บังคับ delivery_date NOT NULL ---
    // ต้องการทั้ง NULL และไม่ NULL จึง "ไม่" ใส่ parent.delivery_date IS NOT NULL

    // กรอง platform ที่ไม่ต้องการ
    $where[] = "parent.platform NOT IN('LuckGems')";

    // ===== กรองช่วงวันที่ (รวม NULL เสมอ) =====
    if (!empty($_GET['date_from'])) {
        $date_from = addslashes($_GET['date_from']);
        $date_to   = addslashes($_GET['date_to'] ?? $_GET['date_from']);

        // ถ้า delivery_date เป็น DATETIME จะครอบทั้งวัน
        $from_start = $date_from . ' 00:00:00';
        $to_end     = $date_to   . ' 23:59:59';

        // รวมแถวที่ delivery_date เป็น NULL ด้วย
        $where[] = "((parent.delivery_date IS NULL) OR (parent.delivery_date BETWEEN '$from_start' AND '$to_end'))";
    }

    // ===== กรองวันเดียว (รวม NULL เสมอ) =====
    if (!empty($_GET['delivery_date'])) {
        $delivery_date = addslashes($_GET['delivery_date']);
        $d_start = $delivery_date . ' 00:00:00';
        $d_end   = $delivery_date . ' 23:59:59';

        // รวมแถวที่ delivery_date เป็น NULL ด้วย
        $where[] = "((parent.delivery_date IS NULL) OR (parent.delivery_date BETWEEN '$d_start' AND '$d_end'))";
    }

    // customer_id
    if (!empty($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        $where[] = "parent.customer_id = $customer_id";
    }

    // combine_mode
    if (isset($_GET['combine_mode'])) {
        $where[] = "parent.delivery_id IS NULL";
    }

    // ค้นหาทั่วไป (code / customer_name / phone)
    if (!empty($search_value)) {
        $sv = addslashes($search_value);
        $where[] = "(parent.code LIKE '%$sv%' 
                 OR parent.customer_name LIKE '%$sv%' 
                  OR parent.delivery_date LIKE '%$sv%' 
                 OR parent.phone LIKE '%$sv%')";
    }

    // รวมเป็น WHERE clause เดียว
    $where_sql = '';
    if (!empty($where)) {
        $where_sql = ' AND ' . implode(' AND ', $where);
    }

    // ====== Count total records ======
    $count_sql = "
        SELECT COUNT(DISTINCT parent.id) AS total
        FROM bs_orders_bwd parent
        LEFT JOIN bs_orders_bwd o 
               ON (o.id = parent.id OR o.parent = parent.id)
        WHERE parent.parent IS NULL
          AND o.status > 0
          $where_sql
    ";
    $count_result   = $dbc->Query($count_sql);
    $total_records  = $dbc->Fetch($count_result)['total'] ?? 0;

    // ====== Main query ======
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
            FORMAT(SUM(o.amount), 4) AS amount,
            FORMAT(SUM(o.price),  2) AS price,
            parent.discount_type,
            FORMAT(SUM(o.discount), 2) AS discount,
            FORMAT(SUM(o.net),      2) AS net,
            FORMAT(SUM(o.total),    2) AS total,
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
            parent.delivery_pack,
            parent.delivery_pack_updated,
            COUNT(o.id) AS item_count,
            os_users.display AS sales,
            bs_deliveries_bwd.code AS delivery_code
        FROM bs_orders_bwd parent
        LEFT JOIN bs_orders_bwd o 
               ON (o.id = parent.id OR o.parent = parent.id)
        LEFT JOIN os_users 
               ON parent.user = os_users.id
        LEFT JOIN bs_deliveries_bwd 
               ON parent.delivery_id = bs_deliveries_bwd.id
        WHERE parent.parent IS NULL
          AND o.status > 0
          $where_sql
        GROUP BY parent.id
        ORDER BY parent.id DESC
        LIMIT $start, $length
    ";

    $result = $dbc->Query($sql);

    $data = [];
    while ($row = $dbc->Fetch($result)) {
        $data[] = $row; // associative array
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
