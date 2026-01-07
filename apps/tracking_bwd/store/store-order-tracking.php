<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

date_default_timezone_set(DEFAULT_TIMEZONE);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    http_response_code(200);
    exit;
}

function clean_date($d)
{
    return (is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) ? $d : null;
}
function clean_int($v)
{
    return intval($v);
}
function like_escape($s)
{
    $s = str_replace('\\', '\\\\', $s);
    $s = str_replace('%', '\%', $s);
    $s = str_replace('_', '\_', $s);
    return $s;
}

$JSON_BODY = [];
if (!empty($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    if ($raw !== false && $raw !== '') {
        $tmp = json_decode($raw, true);
        if (is_array($tmp)) $JSON_BODY = $tmp;
    }
}
$INPUT = array_merge($_GET ?? [], $_POST ?? [], $JSON_BODY);

function inparam($key, $default = null)
{
    global $INPUT;
    return isset($INPUT[$key]) ? $INPUT[$key] : $default;
}

try {
    $dbc = new dbc;
    $dbc->Connect();

    $draw   = intval(inparam('draw', 1));
    $start  = max(0, intval(inparam('start', 0)));
    $length = intval(inparam('length', 25));
    if ($length <= 0) $length = 25;
    if ($length > 500) $length = 500;

    $search_value = trim((string) (inparam('search')['value'] ?? inparam('search_value', '')));

    $date_from = clean_date((string) inparam('date_from', null));
    $date_to   = clean_date((string) inparam('date_to', null));

    $delivery_date = clean_date((string) inparam('delivery_date', null));
    $customer_id   = inparam('customer_id', null);
    $customer_id   = ($customer_id === '' || $customer_id === null) ? null : clean_int($customer_id);
    $combine_mode  = !!inparam('combine_mode', null);

    $tab_type     = inparam('tab_type', null);
    $tab_platform = inparam('tab_platform', null);

    if (!is_string($tab_type)) {
        $tab_type = null;
    }
    if (!is_string($tab_platform)) {
        $tab_platform = null;
    }

    $PLATFORMS = [
        'Facebook',
        'LINE',
        'IG',
        'Shopee',
        'Lazada',
        'Website',
        'LuckGems',
        'TikTok',
        'SilverNow',
        'WalkIN',
        'Exhibition'
    ];

    $order_col_index = intval(inparam('order', [])[0]['column'] ?? 11);
    $order_dir = strtolower((string) (inparam('order', [])[0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

    $DELIV_SAFE = "CASE
      WHEN p.delivery_date IS NULL OR p.delivery_date <= '1000-01-01'
      THEN NULL
      ELSE p.delivery_date
    END";

    $DATE_SAFE = "CASE
      WHEN p.date IS NULL OR p.date <= '1000-01-01 00:00:01'
      THEN NULL
      ELSE DATE(p.date)
    END";

    $columns = [
        "p.id",            // 0 (checkbox)
        "p.id",            // 1 (status/print)
        "p.id",            // 2 (actions)
        "p.code",          // 3
        "d.code",          // 4
        "p.customer_name", // 5
        "c.username",      // 6
        "amount",          // 7 (aggregated)
        "price",           // 8 (aggregated)
        "p.platform",      // 9
        "net",             //10 (aggregated)
        "p.date",          //11 (ordered via $DATE_SAFE)
        "p.delivery_date", //12 (ordered via $DELIV_SAFE)
        "p.orderable_type", //13 (NEW - orderable_type)
        "p.id",            //14
        "u.display",       //15
        "p.Tracking",      //16
        "p.delivery_pack", //17
        "p.id"             //18
    ];
    if (!isset($columns[$order_col_index])) $order_col_index = 11;

    $order_by_primary = "($DELIV_SAFE IS NULL) ASC, $DELIV_SAFE ASC, ($DATE_SAFE IS NULL) ASC, $DATE_SAFE ASC, p.id ASC";

    if ($columns[$order_col_index] === "p.delivery_date") {
        $order_by_primary = "($DELIV_SAFE IS NULL) ASC, $DELIV_SAFE $order_dir, ($DATE_SAFE IS NULL) ASC, $DATE_SAFE $order_dir, p.id $order_dir";
    } elseif ($columns[$order_col_index] === "p.date") {
        $order_by_primary = "($DATE_SAFE IS NULL) ASC, $DATE_SAFE $order_dir, p.id $order_dir";
    } elseif (in_array($columns[$order_col_index], ["amount", "price", "net"])) {
        $order_by_primary = "($DELIV_SAFE IS NULL) ASC, $DELIV_SAFE $order_dir, ($DATE_SAFE IS NULL) ASC, $DATE_SAFE $order_dir, p.id $order_dir";
    } else {
        $order_by_primary = $columns[$order_col_index] . " $order_dir, p.id ASC";
    }

    $where = [];
    $where[] = "p.parent IS NULL";
    $where[] = "p.status > 0";

    // โชว์เฉพาะ order ที่ pack แล้วทั้งหมด (delivery_pack = 1 ทุกกล่อง)
    $where[] = "NOT EXISTS (
        SELECT 1 
        FROM bs_orders_bwd child 
        WHERE child.status > 0 
          AND (child.parent = p.id OR child.id = p.id)
          AND (child.delivery_pack IS NULL OR child.delivery_pack = 0)
    )";

    if ($date_from) {
        $df = $date_from;
        $dt = $date_to ?: $date_from;
        $where[] = "(($DELIV_SAFE BETWEEN '$df' AND '$dt') OR $DELIV_SAFE IS NULL)";
    }

    if ($delivery_date) {
        $where[] = "($DELIV_SAFE = '$delivery_date')";
    }

    if ($customer_id) {
        $where[] = "p.customer_id = " . intval($customer_id);
    }

    if ($combine_mode) {
        $where[] = "p.delivery_id IS NULL";
    }

    if ($tab_type === 'no_delivery') {
        $where[] = "($DELIV_SAFE IS NULL)";
    } elseif ($tab_type === 'platform' && in_array($tab_platform, $PLATFORMS, true)) {
        $where[] = "p.platform = '{$tab_platform}'";
    }

    if ($search_value !== '') {
        if (strlen($search_value) > 2000) {
            $search_value = substr($search_value, 0, 2000);
        }
        $q = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search_value);
        $code_eq = preg_match('/^[A-Za-z0-9._-]+$/', $search_value) ? $search_value : null;

        $cond_code = ($code_eq !== null)
            ? "(p.code = '$code_eq'
               OR EXISTS(SELECT 1 FROM bs_orders_bwd ch WHERE ch.status > 0 AND ch.parent = p.id AND ch.code = '$code_eq')
               OR p.code LIKE '%$q%'
               OR EXISTS(SELECT 1 FROM bs_orders_bwd ch WHERE ch.status > 0 AND ch.parent = p.id AND ch.code LIKE '%$q%'))"
            : "(p.code LIKE '%$q%'
               OR EXISTS(SELECT 1 FROM bs_orders_bwd ch WHERE ch.status > 0 AND ch.parent = p.id AND ch.code LIKE '%$q%'))";

        $where[] = "(
            $cond_code
            OR p.customer_name LIKE '%$q%'
            OR p.phone LIKE '%$q%'
            OR c.username LIKE '%$q%'
            OR p.platform LIKE '%$q%'
            OR CAST($DELIV_SAFE AS CHAR) LIKE '%$q%'
            OR CAST($DATE_SAFE  AS CHAR) LIKE '%$q%'
        )";
    }

    $where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

    $sql_total = "SELECT COUNT(*) AS total FROM bs_orders_bwd p WHERE p.parent IS NULL AND p.status > 0";
    $res_total = $dbc->Query($sql_total);
    $recordsTotal = 0;
    if ($res_total && ($row = $dbc->Fetch($res_total))) $recordsTotal = intval($row['total']);

    $sql_filtered = "
        SELECT COUNT(*) AS total
        FROM bs_orders_bwd p
        LEFT JOIN bs_customers_bwd c ON p.customer_id = c.id
        $where_sql
    ";
    $res_filtered = $dbc->Query($sql_filtered);
    $recordsFiltered = 0;
    if ($res_filtered && ($row = $dbc->Fetch($res_filtered))) $recordsFiltered = intval($row['total']);

    $sql_ids = "
        SELECT p.id
        FROM bs_orders_bwd p
        LEFT JOIN bs_customers_bwd c ON p.customer_id = c.id
        $where_sql
        ORDER BY $order_by_primary
        LIMIT $start, $length
    ";
    $ids = [];
    $res_ids = $dbc->Query($sql_ids);
    if ($res_ids) {
        while ($r = $dbc->Fetch($res_ids)) $ids[] = intval($r['id']);
    }

    if (empty($ids)) {
        $response = [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => [],
            'start'           => $start,
            'length'          => $length
        ];
        if (!empty($INPUT['debug'])) {
            $response['debug_sql_total']    = $sql_total ?? null;
            $response['debug_sql_filtered'] = $sql_filtered ?? null;
            $response['debug_sql_ids']      = $sql_ids ?? null;
            $response['ids']                = $ids;
        }
        echo json_encode($response);
        exit;
    }

    $ids_in = implode(',', array_map('intval', $ids));

    $order_for_main =
        (in_array($columns[$order_col_index], ["amount", "price", "net"]))
        ? ($columns[$order_col_index] . " $order_dir, p.id ASC")
        : $order_by_primary;

    $sql_main = "
    SELECT
      p.id,
      p.customer_id,
      p.code,
      p.customer_name,
      p.phone,
      p.platform,
      $DATE_SAFE   AS date,
      $DELIV_SAFE  AS delivery_date,
      p.user,
      p.delivery_id,
      p.Tracking,
      p.delivery_pack,
      p.orderable_type,
      COALESCE(u.display,'')  AS sales,
      COALESCE(d.code,'')     AS delivery_code,
      COALESCE(c.username,'') AS username,
      agg.amount,
      agg.price,
      agg.discount,
      agg.net,
      agg.total,
      agg.item_count,
      agg.box_count,
      agg.tracking_count
    FROM bs_orders_bwd p
    LEFT JOIN os_users u            ON p.user = u.id
    LEFT JOIN bs_deliveries_bwd d ON p.delivery_id = d.id
    LEFT JOIN bs_customers_bwd c    ON p.customer_id = c.id
    LEFT JOIN (
        SELECT root_id,
               SUM(amount)   AS amount,
               SUM(price)    AS price,
               SUM(discount) AS discount,
               SUM(net)      AS net,
               SUM(total)    AS total,
               SUM(item_cnt) AS item_count,
               MAX(box_number) + 1 AS box_count,
               SUM(has_tracking) AS tracking_count
        FROM (
            /* children of selected roots */
            SELECT
              o.parent AS root_id,
              SUM(o.amount)   AS amount,
              SUM(o.price)    AS price,
              SUM(o.discount) AS discount,
              SUM(o.net)      AS net,
              SUM(o.total)    AS total,
              COUNT(*)        AS item_cnt,
              MAX(o.box_number) AS box_number,
              COUNT(DISTINCT CASE 
                  WHEN o.Tracking IS NOT NULL AND o.Tracking != '' 
                  THEN o.box_number 
                  ELSE NULL 
              END) AS has_tracking
            FROM bs_orders_bwd o
            WHERE o.status > 0
              AND o.parent IN ($ids_in)
            GROUP BY o.parent

            UNION ALL

            /* single root without children */
            SELECT
              o.id   AS root_id,
              SUM(o.amount)   AS amount,
              SUM(o.price)    AS price,
              SUM(o.discount) AS discount,
              SUM(o.net)      AS net,
              SUM(o.total)    AS total,
              COUNT(*)        AS item_cnt,
              MAX(o.box_number) AS box_number,
              COUNT(DISTINCT CASE 
                  WHEN o.Tracking IS NOT NULL AND o.Tracking != '' 
                  THEN o.box_number 
                  ELSE NULL 
              END) AS has_tracking
            FROM bs_orders_bwd o
            WHERE o.status > 0
              AND o.parent IS NULL
              AND o.id IN ($ids_in)
            GROUP BY o.id
        ) X
        GROUP BY root_id
    ) agg ON agg.root_id = p.id
    WHERE p.id IN ($ids_in)
    ORDER BY $order_for_main
";
    $data = [];
    $res_main = $dbc->Query($sql_main);
    if ($res_main) {
        while ($row = $dbc->Fetch($res_main)) {
            foreach (['amount' => 4, 'price' => 2, 'net' => 2, 'total' => 2] as $k => $dec) {
                if (isset($row[$k]) && $row[$k] !== null && $row[$k] !== '') {
                    $row[$k] = number_format((float)$row[$k], $dec, '.', '');
                } else {
                    $row[$k] = number_format(0, $dec, '.', '');
                }
            }
            foreach ($row as $k => $v) {
                if ($v === null) $row[$k] = '';
            }
            $row['DT_RowId'] = 'row_' . $row['id'];
            $data[] = $row;
        }
    }

    $response = [
        'draw'            => $draw,
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data'            => $data,
        'start'           => $start,
        'length'          => $length
    ];
    if (!empty($INPUT['debug'])) {
        $response['debug_sql_total']    = $sql_total ?? null;
        $response['debug_sql_filtered'] = $sql_filtered ?? null;
        $response['debug_sql_ids']      = $sql_ids ?? null;
        $response['debug_sql_main']     = $sql_main ?? null;
        $response['ids']                = $ids;
    }

    echo json_encode($response);
} catch (Exception $e) {
    error_log("DataTable error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage(),
        'data' => [],
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'draw' => isset($draw) ? $draw : 1
    ]);
} finally {
    if (isset($dbc) && $dbc) {
        $dbc->Close();
    }
}
