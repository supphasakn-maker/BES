<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

// Set content type to JSON
header('Content-Type: application/json');

try {
    $dbc = new dbc;
    $dbc->Connect();

    // Parameters from DataTables
    $start = intval($_GET['start'] ?? 0);
    $length = intval($_GET['length'] ?? 25);
    $search_value = $_GET['search']['value'] ?? '';

    // สร้าง WHERE conditions
    $where_conditions = [];

    // Date filter
    if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
        $date_from = addslashes($_GET['date_from']);
        $date_to = addslashes($_GET['date_to'] ?? $_GET['date_from']);
        $where_conditions[] = "((parent.delivery_date BETWEEN '$date_from' AND '$date_to') OR parent.delivery_date IS NULL)";
    }

    // Specific delivery date filter
    if (isset($_GET['delivery_date']) && !empty($_GET['delivery_date'])) {
        $delivery_date = addslashes($_GET['delivery_date']);
        $where_conditions[] = "parent.delivery_date = '$delivery_date'";
    }

    // Customer filter
    if (isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        $where_conditions[] = "parent.customer_id = $customer_id";
    }

    // Combine mode (orders without delivery)
    if (isset($_GET['combine_mode'])) {
        $where_conditions[] = "parent.delivery_id IS NULL";
    }

    // Search condition
    if (!empty($search_value)) {
        $search_value = addslashes($search_value);
        $search_conditions = [
            "parent.code LIKE '%$search_value%'",
            "parent.customer_name LIKE '%$search_value%'", 
            "parent.phone LIKE '%$search_value%'",
            "bs_customers_bwd.username LIKE '%$search_value%'",
            "parent.platform LIKE '%$search_value%'"
        ];
        $where_conditions[] = "(" . implode(' OR ', $search_conditions) . ")";
    }

    // รวม WHERE conditions
    $where_clause = !empty($where_conditions) ? 'AND ' . implode(' AND ', $where_conditions) : '';

    // Count total records (สำหรับ pagination)
    $count_sql = "
        SELECT COUNT(DISTINCT parent.id) as total
        FROM bs_orders_bwd parent
        LEFT JOIN bs_customers_bwd ON parent.customer_id = bs_customers_bwd.id
        WHERE parent.parent IS NULL $where_clause
    ";

    $count_result = $dbc->Query($count_sql);
    $count_row = $dbc->Fetch($count_result);
    $total_records = $count_row ? intval($count_row['total']) : 0;

    // Count filtered records (อาจต่างจาก total ถ้ามี search)
    $filtered_records = $total_records; // default เหมือนกัน

    // Main query - ใช้ subquery แยกการคำนวณ sum เหมือนกับโค้ดที่สอง
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
            
            -- ใช้ค่าจาก child_sum ที่รวม parent + child แล้ว แทน COALESCE
            FORMAT(child_sum.total_amount, 4) as amount,
            FORMAT(child_sum.total_price, 2) as price,
            FORMAT(child_sum.total_discount, 2) as discount,
            FORMAT(child_sum.total_net, 2) as net,
            FORMAT(child_sum.total_total, 2) as total,
            child_sum.item_count as item_count,
            
            COALESCE(os_users.display, '') as sales,
            COALESCE(bs_deliveries_bwd.code, '') as delivery_code,
            COALESCE(bs_deliveries_bwd.code, '') as order_code,
            COALESCE(bs_customers_bwd.username, '') as username,
            COALESCE(bs_deliveries_bwd.payment_note, '') as payment_note,
            COALESCE(bs_deliveries_bwd.billing_id, '') as billing_id
            
        FROM bs_orders_bwd parent
        LEFT JOIN (
            SELECT 
                COALESCE(parent, id) as parent_id,
                COUNT(*) as item_count,
                SUM(amount) as total_amount,
                SUM(price) as total_price,
                SUM(discount) as total_discount,
                SUM(net) as total_net,
                SUM(total) as total_total
            FROM bs_orders_bwd 
            WHERE (parent IS NOT NULL OR (parent IS NULL AND id IN (
                SELECT DISTINCT p.id FROM bs_orders_bwd p WHERE p.parent IS NULL
            ))) AND status > 0
            GROUP BY COALESCE(parent, id)
        ) child_sum ON child_sum.parent_id = parent.id
        LEFT JOIN os_users ON parent.user = os_users.id
        LEFT JOIN bs_deliveries_bwd ON parent.delivery_id = bs_deliveries_bwd.id
        LEFT JOIN bs_customers_bwd ON parent.customer_id = bs_customers_bwd.id
        WHERE parent.parent IS NULL $where_clause
        ORDER BY parent.date ASC
        LIMIT $start, $length
    ";

    $result = $dbc->Query($sql);

    $data = [];
    if ($result) {
        while ($row = $dbc->Fetch($result)) {
            // เพิ่ม DT_RowId สำหรับ DataTables
            $row['DT_RowId'] = 'row_' . $row['id'];
            
            // ตรวจสอบและแปลงค่า NULL
            foreach ($row as $key => $value) {
                if ($value === null) {
                    $row[$key] = '';
                }
            }
            
            // แปลง format ของ delivery_date ถ้ามี
            if (!empty($row['delivery_date']) && $row['delivery_date'] !== '0000-00-00') {
                // ถ้าเป็น datetime ให้เอาแค่ date
                if (strpos($row['delivery_date'], ' ') !== false) {
                    $row['delivery_date'] = explode(' ', $row['delivery_date'])[0];
                }
            } else {
                $row['delivery_date'] = null;
            }
            
            // แปลง format ของ date
            if (!empty($row['date']) && $row['date'] !== '0000-00-00') {
                if (strpos($row['date'], ' ') !== false) {
                    $row['date'] = explode(' ', $row['date'])[0];
                }
            }
            
            // Debug เฉพาะ order ที่มีปัญหา (เช่น OD-0002370, OD-0002371)
            if (strpos($row['code'], 'OD-000237') !== false) {
                error_log("=== DEBUG ORDER: " . $row['code'] . " ===");
                error_log("Parent ID: " . $row['id']);
                error_log("Amount: " . $row['amount'] . " (from child_sum or parent)");
                error_log("Price: " . $row['price']);
                error_log("Net: " . $row['net']);
                error_log("Item Count: " . $row['item_count']);
                
                // Query child records แยกเพื่อตรวจสอบ
                $debug_sql = "SELECT id, amount, price, net, status FROM bs_orders_bwd WHERE parent = " . $row['id'] . " AND status > 0";
                $debug_result = $dbc->Query($debug_sql);
                $total_debug_amount = 0;
                $total_debug_price = 0;
                $total_debug_net = 0;
                $child_count = 0;
                
                while ($debug_row = $dbc->Fetch($debug_result)) {
                    $child_count++;
                    $total_debug_amount += $debug_row['amount'];
                    $total_debug_price += $debug_row['price'];
                    $total_debug_net += $debug_row['net'];
                    error_log("  Child " . $child_count . ": ID=" . $debug_row['id'] . ", Amount=" . $debug_row['amount'] . ", Price=" . $debug_row['price'] . ", Net=" . $debug_row['net']);
                }
                
                error_log("Child Sum - Amount: " . $total_debug_amount . ", Price: " . $total_debug_price . ", Net: " . $total_debug_net);
                error_log("Child Count: " . $child_count);
                error_log("=== END DEBUG ===");
            }
            
            $data[] = $row;
        }
    }

    // สร้าง response ตาม DataTables format
    $response = [
        'draw' => intval($_GET['draw'] ?? 1),
        'recordsTotal' => $total_records,
        'recordsFiltered' => $filtered_records,
        'data' => $data
    ];

    echo json_encode($response);

} catch (Exception $e) {
    // Log error
    error_log("DataTable error: " . $e->getMessage());
    error_log("SQL: " . ($sql ?? 'No SQL'));
    
    // ส่ง error response
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage(),
        'data' => [],
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'draw' => intval($_GET['draw'] ?? 1)
    ]);
} finally {
    if (isset($dbc) && $dbc) {
        $dbc->Close();
    }
}
?>