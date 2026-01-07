<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$date_filter_condition = "";
if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
    $date_filter_condition = " AND DATE(parent.created) = '" . $_GET['date_filter'] . "'";
}

$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_bwd_calc");

$create_temp = "
CREATE TEMPORARY TABLE temp_bwd_calc AS
SELECT 
    parent.id,
    parent.code,
    parent.customer_id,
    parent.customer_name,
    parent.created,
    parent.sales,
    parent.user,
    parent.type,
    parent.parent,
    parent.updated,
    parent.price,
    parent.vat_type,
    parent.delivery_date,
    parent.delivery_time,
    parent.lock_status,
    parent.status,
    parent.comment,
    parent.shipping_address,
    parent.billing_address,
    parent.billing_id,
    parent.info_payment,
    parent.info_contact,
    parent.delivery_id,
    parent.remove_reason,
    parent.product_id,
    SUM(CASE 
        WHEN child.product_id = 1 THEN child.amount * 0.015
        WHEN child.product_id = 2 THEN child.amount * 0.050
        WHEN child.product_id = 3 THEN child.amount * 0.150
        ELSE 0
    END) AS calculated_amount,
    SUM(child.total) AS total_sum,
    COALESCE((SELECT SUM(split_amount) FROM bs_orders_split_bwd WHERE parent_order_id = parent.id AND status = 1), 0) AS total_split_amount,
    COALESCE((SELECT SUM(split_total) FROM bs_orders_split_bwd WHERE parent_order_id = parent.id AND status = 1), 0) AS total_split_total,
    0 AS is_split,
    NULL AS split_id,
    NULL AS split_parent_id
FROM bs_orders_bwd parent
LEFT JOIN bs_orders_bwd child ON (child.id = parent.id OR child.parent = parent.id)
WHERE parent.status > 0
AND DATE(parent.created) >= '2025-10-01'
AND (parent.parent IS NULL OR parent.parent = 0)
AND child.product_id IN (1,2,3)
AND child.status > 0
{$date_filter_condition}
GROUP BY parent.id
";

$dbc->Query($create_temp);

$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_split_orders");
$create_split_temp = "
CREATE TEMPORARY TABLE temp_split_orders AS
SELECT 
    CONCAT('SPLIT_', s.id) AS id,
    CONCAT(o.code, '-S', s.id) AS code,
    o.customer_id,
    o.customer_name,
    o.created,
    o.sales,
    o.user,
    o.type,
    o.parent,
    o.updated,
    o.price,
    o.vat_type,
    o.delivery_date,
    o.delivery_time,
    o.lock_status,
    o.status,
    o.comment,
    o.shipping_address,
    o.billing_address,
    o.billing_id,
    o.info_payment,
    o.info_contact,
    o.delivery_id,
    o.remove_reason,
    o.product_id,
    s.split_amount AS calculated_amount,
    s.split_total AS total_sum,
    0 AS total_split_amount,
    0 AS total_split_total,
    1 AS is_split,
    s.id AS split_id,
    s.parent_order_id AS split_parent_id
FROM bs_orders_split_bwd s
INNER JOIN temp_bwd_calc o ON s.parent_order_id = o.id
WHERE s.status = 1
";
$dbc->Query($create_split_temp);

$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_all_orders");
$union_query = "
CREATE TEMPORARY TABLE temp_all_orders AS
SELECT 
    id, code, customer_id, customer_name, created, sales, user, type, parent, updated,
    price, vat_type, delivery_date, delivery_time, lock_status, status, comment,
    shipping_address, billing_address, billing_id, info_payment, info_contact,
    delivery_id, remove_reason, product_id,
    (calculated_amount - total_split_amount) AS calculated_amount,
    (total_sum - total_split_total) AS total_sum,
    total_split_amount, total_split_total, is_split, split_id, split_parent_id
FROM temp_bwd_calc
WHERE calculated_amount > total_split_amount

UNION ALL

SELECT 
    id, code, customer_id, customer_name, created, sales, user, type, parent, updated,
    price, vat_type, delivery_date, delivery_time, lock_status, status, comment,
    shipping_address, billing_address, billing_id, info_payment, info_contact,
    delivery_id, remove_reason, product_id,
    calculated_amount, total_sum, total_split_amount, total_split_total, is_split, split_id, split_parent_id
FROM temp_split_orders
";
$dbc->Query($union_query);

$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_all_orders_with_mapping");
$mapping_query = "
CREATE TEMPORARY TABLE temp_all_orders_with_mapping AS
SELECT 
    t.*,
    bmp.order_id AS mapping_true,
    bmp.id AS mapping_id,
    bmp.mapping_id AS mapping,
    bmu.order_id AS mapping_true_usd,
    bmu.id AS mapping_id_usd,
    bmu.mapping_id AS mapping_usd
FROM temp_all_orders t
LEFT JOIN bs_mapping_profit_orders_bwd bmp ON CAST(bmp.order_id AS CHAR) = CAST(t.id AS CHAR)
LEFT JOIN bs_mapping_profit_orders_usd_bwd bmu ON CAST(bmu.order_id AS CHAR) = CAST(t.id AS CHAR)
";
$dbc->Query($mapping_query);

$columns = array(
    "id" => "temp_all_orders_with_mapping.id",
    "code" => "temp_all_orders_with_mapping.code",
    "customer_id" => "temp_all_orders_with_mapping.customer_id",
    "customer_name" => "temp_all_orders_with_mapping.customer_name",
    "created" => "DATE(temp_all_orders_with_mapping.created)",
    "sales" => "temp_all_orders_with_mapping.sales",
    "user" => "temp_all_orders_with_mapping.user",
    "type" => "temp_all_orders_with_mapping.type",
    "parent" => "temp_all_orders_with_mapping.parent",
    "updated" => "temp_all_orders_with_mapping.updated",
    "price" => "FORMAT(temp_all_orders_with_mapping.price,0)",
    "vat_type" => "temp_all_orders_with_mapping.vat_type",
    "calculated_amount" => "temp_all_orders_with_mapping.calculated_amount",
    "total_sum" => "temp_all_orders_with_mapping.total_sum",
    "delivery_date" => "temp_all_orders_with_mapping.delivery_date",
    "delivery_time" => "temp_all_orders_with_mapping.delivery_time",
    "lock_status" => "temp_all_orders_with_mapping.lock_status",
    "status" => "temp_all_orders_with_mapping.status",
    "comment" => "temp_all_orders_with_mapping.comment",
    "shipping_address" => "temp_all_orders_with_mapping.shipping_address",
    "billing_address" => "temp_all_orders_with_mapping.billing_address",
    "billing_id" => "temp_all_orders_with_mapping.billing_id",
    "info_payment" => "temp_all_orders_with_mapping.info_payment",
    "info_contact" => "temp_all_orders_with_mapping.info_contact",
    "delivery_id" => "temp_all_orders_with_mapping.delivery_id",
    "remove_reason" => "temp_all_orders_with_mapping.remove_reason",
    "product_id" => "temp_all_orders_with_mapping.product_id",
    "is_split" => "temp_all_orders_with_mapping.is_split",
    "split_id" => "temp_all_orders_with_mapping.split_id",
    "split_parent_id" => "temp_all_orders_with_mapping.split_parent_id",
    "mapping_true" => "temp_all_orders_with_mapping.mapping_true",
    "mapping_id" => "temp_all_orders_with_mapping.mapping_id",
    "mapping" => "temp_all_orders_with_mapping.mapping",
    "mapping_true_usd" => "temp_all_orders_with_mapping.mapping_true_usd",
    "mapping_id_usd" => "temp_all_orders_with_mapping.mapping_id_usd",
    "mapping_usd" => "temp_all_orders_with_mapping.mapping_usd",
);

$where = "1=1";

if (isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
    $where .= " AND (DATE(temp_all_orders_with_mapping.created) = '" . $_GET['date_filter'] . "' 
               OR EXISTS (
                   SELECT 1 FROM bs_mapping_profit_sumusd_bwd bms 
                   INNER JOIN bs_mapping_profit_orders_usd_bwd bmu ON bms.id = bmu.mapping_id 
                   WHERE CAST(bmu.order_id AS CHAR) = CAST(temp_all_orders_with_mapping.id AS CHAR)
                   AND DATE(bms.mapped) = '" . $_GET['date_filter'] . "'
               )
               OR EXISTS (
                   SELECT 1 FROM bs_mapping_profit_bwd bmp_main
                   INNER JOIN bs_mapping_profit_orders_bwd bmp_orders ON bmp_main.id = bmp_orders.mapping_id 
                   WHERE CAST(bmp_orders.order_id AS CHAR) = CAST(temp_all_orders_with_mapping.id AS CHAR)
                   AND DATE(bmp_main.mapped) = '" . $_GET['date_filter'] . "'
               ))";
}

$table = array(
    "index" => "id",
    "name" => "temp_all_orders_with_mapping",
    "where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$data = $dbc->GetResult();

for ($i = 0; $i < count($data['aaData']); $i++) {
    $data['aaData'][$i]['DT_RowId'] = $data['aaData'][$i]['id'];
    $data['aaData'][$i]['calculated_amount'] = number_format($data['aaData'][$i]['calculated_amount'], 4, '.', '');
    $data['aaData'][$i]['total'] = number_format($data['aaData'][$i]['total_sum'], 0);

    if ($data['aaData'][$i]['mapping_id'] != null) {
        $data['aaData'][$i]['mapping_item_id'] = $data['aaData'][$i]['mapping_id'];
    }
}

$summary_sql = "
SELECT 
    SUM(CASE WHEN mapping_true IS NULL AND mapping_true_usd IS NULL THEN calculated_amount ELSE 0 END) AS unmatch_amount,
    SUM(CASE WHEN mapping_true IS NULL AND mapping_true_usd IS NULL THEN total_sum ELSE 0 END) AS unmatch_total,
    SUM(CASE WHEN mapping_true IS NOT NULL THEN calculated_amount ELSE 0 END) AS match_thb_amount,
    SUM(CASE WHEN mapping_true IS NOT NULL THEN total_sum ELSE 0 END) AS match_thb_total,
    SUM(CASE WHEN mapping_true_usd IS NOT NULL THEN calculated_amount ELSE 0 END) AS match_usd_amount,
    SUM(CASE WHEN mapping_true_usd IS NOT NULL THEN total_sum ELSE 0 END) AS match_usd_total
FROM temp_all_orders_with_mapping
";

$rst = $dbc->Query($summary_sql);
$summary = $dbc->Fetch($rst);

$data['total'] = array(
    "remain_unmatch" => $summary[0] ? $summary[0] : 0,
    "remain_matching" => 0,
    "remain_total" => $summary[0] ? $summary[0] : 0,
    "remain_price" => $summary[1] ? $summary[1] : 0,
    "remain_matchthbamount" => $summary[2] ? $summary[2] : 0,
    "remain_matchthbday" => $summary[3] ? $summary[3] : 0,
    "remain_matchusdamount" => $summary[4] ? $summary[4] : 0,
    "remain_matchusdday" => $summary[5] ? $summary[5] : 0
);

echo json_encode($data);

$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_bwd_calc");
$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_split_orders");
$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_all_orders");
$dbc->Query("DROP TEMPORARY TABLE IF EXISTS temp_all_orders_with_mapping");

$dbc->Close();
