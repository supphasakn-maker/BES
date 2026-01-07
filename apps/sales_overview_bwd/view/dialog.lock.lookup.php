<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/demo.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

class myModel extends imodal
{
    function body()
    {
        $dbc = $this->dbc;
        $demo = new demo;

        $total_sum_price  = 0;
        $total_sum_net    = 0;
        $total_sum_amount = 0;
        $total_orders     = 0;

        $sum_bars_15_plain   = 0;
        $sum_by_product_type = [];

        if (!function_exists('normalize_name')) {
            function normalize_name($s)
            {
                $s = trim((string)$s);
                $s = str_replace(["\xE2\x80\x93", "–", "—"], "-", $s);
                $s = preg_replace('/\s+/u', '', $s);
                if (function_exists('mb_strtolower')) {
                    return mb_strtolower($s, 'UTF-8');
                }
                return strtolower($s);
            }
        }

        // แปลงค่า orderable_type -> ข้อความไทย
        if (!function_exists('get_orderable_type_label')) {
            function get_orderable_type_label($val)
            {
                $val = trim((string)$val);
                switch ($val) {
                    case 'delivered_by_company':
                        return 'จัดส่งโดยรถบริษัท';
                    case 'post_office':
                        return 'จัดส่งโดยไปรษณีย์ไทย';
                    case 'receive_at_company':
                        return 'รับสินค้าที่บริษัท';
                    case 'receive_at_luckgems':
                        return 'รับสินค้าที่ Luck Gems';
                    default:
                        return '-';
                }
            }
        }

        $TARGET_NAME = normalize_name('แท่ง 15 - หลังเรียบ');
?>
        <div class="text-center text-dark">
            <h3>Lock Report(ที่ยังไม่ได้กำหนดวันจัดส่ง)</h3>
        </div>

        <!-- Mobile responsive styles -->
        <style>
            @media (max-width: 768px) {
                .table-responsive {
                    font-size: 12px;
                }

                .table th,
                .table td {
                    padding: 4px 2px;
                    vertical-align: top;
                }

                .table th {
                    font-size: 11px;
                    white-space: nowrap;
                }

                .mobile-hide {
                    display: none;
                }

                .mobile-stack {
                    display: block;
                    width: 100%;
                    margin-bottom: 5px;
                }

                .card-body {
                    padding: 10px;
                }

                .row {
                    margin-bottom: 5px;
                }

                .col-6 {
                    font-size: 12px;
                }

                .summary-cards .col-md-3 {
                    margin-bottom: 10px;
                }

                .summary-cards .card {
                    margin-bottom: 5px;
                }

                .summary-cards h4 {
                    font-size: 16px;
                }

                .summary-cards p {
                    font-size: 11px;
                }

                .product-table {
                    display: none;
                }

                .product-mobile {
                    font-size: 10px;
                }
            }

            @media (max-width: 576px) {
                .table-responsive {
                    font-size: 10px;
                }

                .table th,
                .table td {
                    padding: 2px 1px;
                }

                h3 {
                    font-size: 18px;
                }

                .summary-cards .col-md-3 {
                    flex: 0 0 50%;
                    max-width: 50%;
                }
            }

            .product-table .table-sm th,
            .product-table .table-sm td {
                padding: 2px 4px;
                border: none;
            }

            .product-table .table-sm thead th {
                border-bottom: 1px solid #dee2e6;
                font-weight: bold;
                background-color: #f8f9fa;
            }

            /* Styles for Product Type Section */
            .product-type-row {
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .product-type-row:hover {
                background-color: #e9ecef;
            }

            .order-details-row {
                background-color: #f8f9fa;
            }

            .order-details-table {
                font-size: 0.9em;
                margin: 0;
            }

            .collapse-icon {
                transition: transform 0.2s;
            }

            .product-type-row.collapsed .collapse-icon {
                transform: rotate(-90deg);
            }
        </style>

        <div class="table-responsive">
            <table id="tblPurchaseLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="text-center">วันสั่ง</th>
                        <th class="text-center">Order ID</th>
                        <th class="text-center">ลูกค้า</th>
                        <th class="text-center mobile-hide">สินค้า</th>
                        <th class="text-center mobile-hide">ประเภท</th>
                        <th class="text-center mobile-hide">วิธีจัดส่ง</th>
                        <th class="text-right">จำนวน</th>
                        <th class="text-right mobile-hide">บาท/กิโล</th>
                        <th class="text-right">ยอดรวม</th>
                        <th class="text-center mobile-hide">ผู้ขาย</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "SELECT * FROM bs_orders_bwd 
                            WHERE delivery_date IS NULL
                              AND date > '2023-12-31'
                              AND parent IS NULL
                              AND status > 0
                            ORDER BY date ASC, id ASC";
                        $rst = $dbc->Query($sql);

                        if ($rst) {
                            while ($order = $dbc->Fetch($rst)) {
                                $parent_amount = (isset($order['amount']) && is_numeric($order['amount'])) ? floatval($order['amount']) : 0;
                                $parent_price  = (isset($order['price'])  && is_numeric($order['price']))  ? floatval($order['price'])  : 0;
                                $parent_net    = (isset($order['net'])    && is_numeric($order['net']))    ? floatval($order['net'])    : 0;

                                // รวมจาก sub orders
                                $sub_amount = 0;
                                $sub_price  = 0;
                                $sub_net    = 0;

                                $sub_sql = "SELECT amount, price, net 
                                        FROM bs_orders_bwd 
                                        WHERE parent = " . intval($order['id']) . " AND status > 0";
                                $sub_rst = $dbc->Query($sub_sql);
                                if ($sub_rst) {
                                    while ($sub_order = $dbc->Fetch($sub_rst)) {
                                        $sub_amount += (isset($sub_order['amount']) && is_numeric($sub_order['amount'])) ? floatval($sub_order['amount']) : 0;
                                        $sub_price  += (isset($sub_order['price'])  && is_numeric($sub_order['price']))  ? floatval($sub_order['price'])  : 0;
                                        $sub_net    += (isset($sub_order['net'])    && is_numeric($sub_order['net']))    ? floatval($sub_order['net'])    : 0;
                                    }
                                }

                                $final_amount = $parent_amount + $sub_amount;
                                $final_price  = $parent_price  + $sub_price;
                                $final_net    = $parent_net    + $sub_net;

                                $customer_name = isset($order['customer_name']) ? trim($order['customer_name']) : '';
                                $phone         = isset($order['phone']) ? trim($order['phone']) : '';
                                $platform      = isset($order['platform']) ? trim($order['platform']) : '';

                                if (empty($customer_name) && isset($order['customer_id']) && intval($order['customer_id']) > 0) {
                                    $customer_sql = "SELECT name, phone FROM bs_customers WHERE id = " . intval($order['customer_id']);
                                    $customer_rst = $dbc->Query($customer_sql);
                                    if ($customer_rst && ($customer_data = $dbc->Fetch($customer_rst))) {
                                        if (empty($customer_name) && isset($customer_data['name'])) {
                                            $customer_name = trim($customer_data['name']);
                                        }
                                        if (empty($phone) && isset($customer_data['phone'])) {
                                            $phone = trim($customer_data['phone']);
                                        }
                                    }
                                }

                                $products        = [];
                                $product_types   = [];
                                $product_details = [];

                                $parent_product_name = null;
                                if (isset($order['product_id']) && intval($order['product_id']) > 0) {
                                    $parent_product_sql = "SELECT name FROM bs_products_bwd WHERE id = " . intval($order['product_id']);
                                    $parent_product_rst = $dbc->Query($parent_product_sql);
                                    if ($parent_product_rst && ($parent_product_data = $dbc->Fetch($parent_product_rst))) {
                                        if (!empty(trim($parent_product_data['name'] ?? ''))) {
                                            $product_name = trim($parent_product_data['name']);
                                            $parent_product_name = $product_name;

                                            $parent_amount_show = $parent_amount;
                                            if ($parent_amount_show > 0) {
                                                $product_details[] = $product_name . ": " . number_format($parent_amount_show, 0) . " แท่ง";
                                            }
                                            $products[] = $product_name;

                                            if ($parent_amount_show > 0 && normalize_name($product_name) === $TARGET_NAME) {
                                                $sum_bars_15_plain += $parent_amount_show;
                                            }
                                        }
                                    }
                                }

                                $parent_type_name = null;
                                if (isset($order['product_type']) && intval($order['product_type']) > 0) {
                                    $parent_type_sql = "SELECT name FROM bs_products_type WHERE id = " . intval($order['product_type']);
                                    $parent_type_rst = $dbc->Query($parent_type_sql);
                                    if ($parent_type_rst && ($parent_type_data = $dbc->Fetch($parent_type_rst))) {
                                        if (!empty(trim($parent_type_data['name'] ?? ''))) {
                                            $type_name = trim($parent_type_data['name']);
                                            $product_types[]   = $type_name;
                                            $parent_type_name  = $type_name;

                                            if ($parent_amount > 0) {
                                                if (!isset($sum_by_product_type[$type_name])) $sum_by_product_type[$type_name] = 0;
                                                $sum_by_product_type[$type_name] += $parent_amount;
                                            }
                                        }
                                    }
                                }

                                $sub_product_sql = "SELECT p.name AS product_name, pt.name AS product_type_name, o.amount
                                                FROM bs_orders_bwd o
                                                LEFT JOIN bs_products_bwd p ON o.product_id = p.id
                                                LEFT JOIN bs_products_type pt ON o.product_type = pt.id
                                                WHERE o.parent = " . intval($order['id']) . "
                                                  AND o.status > 0
                                                ORDER BY p.name, pt.name";
                                $sub_product_rst = $dbc->Query($sub_product_sql);
                                if ($sub_product_rst) {
                                    while ($sub_product_row = $dbc->Fetch($sub_product_rst)) {
                                        $sub_product_name = isset($sub_product_row['product_name']) ? trim($sub_product_row['product_name']) : '';
                                        $sub_type_name    = isset($sub_product_row['product_type_name']) ? trim($sub_product_row['product_type_name']) : '';
                                        $sub_amount_each  = (isset($sub_product_row['amount']) && is_numeric($sub_product_row['amount'])) ? floatval($sub_product_row['amount']) : 0;

                                        if ($sub_product_name !== '') {
                                            if ($sub_amount_each > 0 && normalize_name($sub_product_name) === $TARGET_NAME) {
                                                $sum_bars_15_plain += $sub_amount_each;
                                            }

                                            $full_product_name = $sub_product_name;
                                            if ($sub_type_name !== '') {
                                                $full_product_name .= " (" . $sub_type_name . ")";
                                                $product_types[] = $sub_type_name;

                                                if ($sub_amount_each > 0) {
                                                    if (!isset($sum_by_product_type[$sub_type_name])) $sum_by_product_type[$sub_type_name] = 0;
                                                    $sum_by_product_type[$sub_type_name] += $sub_amount_each;
                                                }
                                            }

                                            if ($sub_amount_each > 0) {
                                                $product_details[] = $full_product_name . ": " . number_format($sub_amount_each, 0) . " แท่ง";
                                            }
                                            $products[] = $sub_product_name;
                                        }
                                    }
                                }

                                if (!empty($product_details)) {
                                    $product_display  = '<table class="table table-sm table-borderless mb-0" style="font-size: 11px;">';
                                    $product_display .= '<thead><tr><th>สินค้า</th><th class="text-right">จำนวน</th></tr></thead>';
                                    $product_display .= '<tbody>';
                                    foreach ($product_details as $detail) {
                                        $parts = explode(': ', $detail, 2);
                                        if (count($parts) === 2) {
                                            $product_name_show = $parts[0];
                                            $amount_show       = $parts[1];
                                            $product_display  .= '<tr><td>' . htmlspecialchars($product_name_show) . '</td><td class="text-right"><strong>' . $amount_show . '</strong></td></tr>';
                                        }
                                    }
                                    $product_display .= '</tbody></table>';
                                } else {
                                    $product_display = empty($products) ? "-" : implode(", ", array_unique($products));
                                }

                                $product_type_display = empty($product_types) ? "-" : implode(", ", array_unique($product_types));

                                $customer_display = !empty($customer_name)
                                    ? htmlspecialchars($customer_name)
                                    : ((isset($order['customer_id']) && intval($order['customer_id']) > 0)
                                        ? "ลูกค้า ID: " . intval($order['customer_id'])
                                        : "-");
                                if (!empty($phone)) {
                                    $customer_display .= "<br><small>" . htmlspecialchars($phone) . "</small>";
                                }
                                if (!empty($platform)) {
                                    $customer_display .= "<br><small class='text-muted'>(" . htmlspecialchars($platform) . ")</small>";
                                }

                                $total_sum_amount += $final_amount;
                                $total_sum_price  += $final_price;
                                $total_sum_net    += $final_net;
                                $total_orders++;

                                // แปลง orderable_type เป็นข้อความไทย
                                $orderable_type_raw   = isset($order['orderable_type']) ? $order['orderable_type'] : '';
                                $orderable_type_label = get_orderable_type_label($orderable_type_raw);

                                echo '<tr>';

                                $date_show = '';
                                if (!empty($order['date'])) {
                                    $ts = strtotime($order['date']);
                                    if ($ts) $date_show = date('d/m', $ts);
                                }
                                echo '<td class="text-center">' . htmlspecialchars($date_show) . '</td>';

                                $code_show = isset($order['code']) ? str_replace('OD-', '', (string)$order['code']) : '';
                                echo '<td class="text-center">' . htmlspecialchars($code_show) . '</td>';

                                echo '<td class="text-center">';
                                echo '<div class="mobile-stack">' . $customer_display . '</div>';

                                if (!empty($product_details)) {
                                    echo '<div class="mobile-stack d-md-none product-mobile"><small><strong>สินค้า:</strong><br>';
                                    foreach ($product_details as $detail) {
                                        $parts = explode(': ', $detail, 2);
                                        if (count($parts) === 2) {
                                            echo '• ' . htmlspecialchars($parts[0]) . ': <strong>' . htmlspecialchars($parts[1]) . '</strong><br>';
                                        }
                                    }
                                    echo '</small></div>';
                                }

                                if (isset($order['sales']) && intval($order['sales']) > 0) {
                                    $sales_sql = "SELECT * FROM os_users WHERE id = " . intval($order['sales']);
                                    $sales_rst = $dbc->Query($sales_sql);
                                    if ($sales_rst && ($employee = $dbc->Fetch($sales_rst))) {
                                        $sales_name = '-';
                                        if (!empty($employee['display']))      $sales_name = $employee['display'];
                                        elseif (!empty($employee['name']))     $sales_name = $employee['name'];
                                        elseif (!empty($employee['username'])) $sales_name = $employee['username'];
                                        echo '<div class="mobile-stack d-md-none"><small><strong>ผู้ขาย:</strong> ' . htmlspecialchars($sales_name) . '</small></div>';
                                    }
                                }
                                echo '</td>';

                                echo '<td class="text-left mobile-hide product-table">' . $product_display . '</td>';
                                echo '<td class="text-center mobile-hide">' . htmlspecialchars($product_type_display) . '</td>';
                                echo '<td class="text-center mobile-hide">' . htmlspecialchars($orderable_type_label) . '</td>';

                                echo '<td class="text-right">' . number_format($final_amount, 0) . '</td>';
                                echo '<td class="text-right mobile-hide">' . number_format($final_price, 0) . '</td>';
                                echo '<td class="text-right"><strong>' . number_format($final_net, 0) . '</strong></td>';

                                echo '<td class="text-center mobile-hide">';
                                if (isset($order['sales']) && intval($order['sales']) > 0) {
                                    $sales_sql = "SELECT * FROM os_users WHERE id = " . intval($order['sales']);
                                    $sales_rst = $dbc->Query($sales_sql);
                                    if ($sales_rst && ($employee = $dbc->Fetch($sales_rst))) {
                                        $sales_name = '-';
                                        if (!empty($employee['display']))      $sales_name = $employee['display'];
                                        elseif (!empty($employee['name']))     $sales_name = $employee['name'];
                                        elseif (!empty($employee['username'])) $sales_name = $employee['username'];
                                        echo htmlspecialchars($sales_name);
                                    } else {
                                        echo "-";
                                    }
                                } else {
                                    echo "-";
                                }
                                echo '</td>';

                                echo '</tr>';
                            }
                        }
                    } catch (Exception $e) {
                        echo '<tr><td colspan="10" class="text-center text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="d-none d-md-table-row" style="background-color:#f8f9fa;font-weight:bold;">
                        <td colspan="6" class="text-right">
                            รวมทั้งหมด (<?php echo number_format($total_orders); ?> รายการ):
                        </td>
                        <td class="text-right"><?php echo number_format($total_sum_amount, 0); ?></td>
                        <td class="text-right"><?php echo number_format($total_sum_price, 0); ?></td>
                        <td class="text-right"><strong><?php echo number_format($total_sum_net, 0); ?></strong></td>
                        <td class="text-center">-</td>
                    </tr>
                    <tr class="d-table-row d-md-none" style="background-color:#f8f9fa;font-weight:bold;">
                        <td colspan="3" class="text-right">
                            รวม (<?php echo number_format($total_orders); ?> รายการ):
                        </td>
                        <td class="text-right mobile-hide">-</td>
                        <td class="text-right mobile-hide">-</td>
                        <td class="text-right mobile-hide">-</td>
                        <td class="text-right"><?php echo number_format($total_sum_amount, 0); ?></td>
                        <td class="text-right mobile-hide">-</td>
                        <td class="text-right"><strong><?php echo number_format($total_sum_net, 0); ?></strong></td>
                        <td class="text-center mobile-hide">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row mt-3">

            <div class="col-md-12 mt-3 mt-md-0">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <strong>สรุปตามประเภทสินค้า (Product Type)</strong>
                        <small class="float-right"><i class="fas fa-info-circle"></i> คลิกเพื่อดูรายละเอียด Order</small>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($sum_by_product_type)) : ?>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 50px;"></th>
                                            <th>ประเภทสินค้า</th>
                                            <th class="text-right">จำนวนแท่ง</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        arsort($sum_by_product_type);
                                        $type_index = 0;
                                        foreach ($sum_by_product_type as $type_name => $qty) :
                                            $type_index++;
                                            $orders_in_type = [];

                                            $escaped_type_name = addslashes($type_name);

                                            $type_sql = "SELECT DISTINCT o.id, o.code, o.date, o.customer_name, o.customer_id,
                                            c.name as customer_db_name, c.phone as customer_phone,
                                            o.amount, o.net, o.sales
                                            FROM bs_orders_bwd o
                                            LEFT JOIN bs_customers c ON o.customer_id = c.id
                                            LEFT JOIN bs_products_type pt ON o.product_type = pt.id
                                            WHERE o.delivery_date IS NULL
                                              AND o.date > '2023-12-31'
                                              AND o.parent IS NULL
                                              AND o.status > 0
                                              AND pt.name = '" . $escaped_type_name . "'
                                            
                                            UNION
                                            
                                            SELECT DISTINCT parent.id, parent.code, parent.date, parent.customer_name, parent.customer_id,
                                            c.name as customer_db_name, c.phone as customer_phone,
                                            parent.amount, parent.net, parent.sales
                                            FROM bs_orders_bwd parent
                                            LEFT JOIN bs_customers c ON parent.customer_id = c.id
                                            INNER JOIN bs_orders_bwd sub ON parent.id = sub.parent
                                            LEFT JOIN bs_products_type pt ON sub.product_type = pt.id
                                            WHERE parent.delivery_date IS NULL
                                              AND parent.date > '2023-12-31'
                                              AND parent.parent IS NULL
                                              AND parent.status > 0
                                              AND sub.status > 0
                                              AND pt.name = '" . $escaped_type_name . "'
                                            ORDER BY date ASC, id ASC";

                                            $type_rst = $dbc->Query($type_sql);

                                            if ($type_rst) {
                                                while ($order_row = $dbc->Fetch($type_rst)) {
                                                    $order_amount_this_type = 0;
                                                    $order_net_total = 0;

                                                    $parent_check_sql = "SELECT o.amount, o.net, pt.name 
                                                            FROM bs_orders_bwd o
                                                            LEFT JOIN bs_products_type pt ON o.product_type = pt.id
                                                            WHERE o.id = " . intval($order_row['id']) . "
                                                            AND pt.name = '" . $escaped_type_name . "'";
                                                    $parent_check_rst = $dbc->Query($parent_check_sql);
                                                    if ($parent_check_rst && ($parent_check = $dbc->Fetch($parent_check_rst))) {
                                                        $order_amount_this_type += floatval($parent_check['amount']);
                                                    }

                                                    $sub_type_sql = "SELECT SUM(o.amount) as total_amount
                                                        FROM bs_orders_bwd o
                                                        LEFT JOIN bs_products_type pt ON o.product_type = pt.id
                                                        WHERE o.parent = " . intval($order_row['id']) . "
                                                        AND o.status > 0
                                                        AND pt.name = '" . $escaped_type_name . "'";
                                                    $sub_type_rst = $dbc->Query($sub_type_sql);
                                                    if ($sub_type_rst && ($sub_type = $dbc->Fetch($sub_type_rst))) {
                                                        $order_amount_this_type += floatval($sub_type['total_amount']);
                                                    }

                                                    $order_row['amount_this_type'] = $order_amount_this_type;

                                                    $order_net_total = floatval($order_row['net']);
                                                    $sub_net_sql = "SELECT SUM(net) as total_net FROM bs_orders_bwd WHERE parent = " . intval($order_row['id']) . " AND status > 0";
                                                    $sub_net_rst = $dbc->Query($sub_net_sql);
                                                    if ($sub_net_rst && ($sub_net = $dbc->Fetch($sub_net_rst))) {
                                                        $order_net_total += floatval($sub_net['total_net']);
                                                    }
                                                    $order_row['net_total'] = $order_net_total;

                                                    $orders_in_type[] = $order_row;
                                                }
                                            }
                                        ?>
                                            <tr class="product-type-row collapsed" data-toggle="collapse" data-target="#type-detail-<?php echo $type_index; ?>">
                                                <td class="text-center">
                                                    <i class="fas fa-chevron-down collapse-icon"></i>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($type_name); ?></strong></td>
                                                <td class="text-right"><strong><?php echo number_format($qty, 0); ?></strong></td>
                                            </tr>
                                            <tr class="collapse order-details-row" id="type-detail-<?php echo $type_index; ?>">
                                                <td colspan="3" class="p-3">
                                                    <?php if (!empty($orders_in_type)) : ?>
                                                        <table class="table table-bordered table-sm order-details-table mb-0">
                                                            <thead>
                                                                <tr class="bg-light">
                                                                    <th>วันสั่ง</th>
                                                                    <th>Order ID</th>
                                                                    <th>ลูกค้า</th>
                                                                    <th class="text-right">จำนวน</th>
                                                                    <th class="text-right">ยอดรวม</th>
                                                                    <th>ผู้ขาย</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($orders_in_type as $order_detail) :
                                                                    $customer_display = !empty($order_detail['customer_name'])
                                                                        ? htmlspecialchars($order_detail['customer_name'])
                                                                        : (!empty($order_detail['customer_db_name'])
                                                                            ? htmlspecialchars($order_detail['customer_db_name'])
                                                                            : (intval($order_detail['customer_id']) > 0 ? 'ลูกค้า ID: ' . intval($order_detail['customer_id']) : '-'));

                                                                    if (!empty($order_detail['customer_phone'])) {
                                                                        $customer_display .= '<br><small>' . htmlspecialchars($order_detail['customer_phone']) . '</small>';
                                                                    }

                                                                    $sales_name = '-';
                                                                    if (intval($order_detail['sales']) > 0) {
                                                                        $sales_sql = "SELECT display, name FROM os_users WHERE id = " . intval($order_detail['sales']);
                                                                        $sales_rst = $dbc->Query($sales_sql);
                                                                        if ($sales_rst && ($sales_data = $dbc->Fetch($sales_rst))) {
                                                                            if (!empty($sales_data['display'])) {
                                                                                $sales_name = $sales_data['display'];
                                                                            } elseif (!empty($sales_data['name'])) {
                                                                                $sales_name = $sales_data['name'];
                                                                            }
                                                                        }
                                                                    }

                                                                    $date_show = '';
                                                                    if (!empty($order_detail['date'])) {
                                                                        $ts = strtotime($order_detail['date']);
                                                                        if ($ts) $date_show = date('d/m/y', $ts);
                                                                    }
                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($date_show); ?></td>
                                                                        <td>
                                                                            <a href="#apps/schedule_bwd_2/index.php?view=printablemulti&order_id=<?php echo intval($order_detail['id']); ?>"

                                                                                target="_blank"
                                                                                class="text-primary"
                                                                                style="text-decoration: none;">
                                                                                <strong><?php echo htmlspecialchars(str_replace('OD-', '', $order_detail['code'])); ?></strong>
                                                                            </a>
                                                                        </td>
                                                                        <td><?php echo $customer_display; ?></td>
                                                                        <td class="text-right"><strong><?php echo number_format($order_detail['amount_this_type'], 0); ?></strong></td>
                                                                        <td class="text-right"><?php echo number_format($order_detail['net_total'], 0); ?></td>
                                                                        <td><?php echo htmlspecialchars($sales_name); ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="bg-light font-weight-bold">
                                                                    <td colspan="3" class="text-right">รวม:</td>
                                                                    <td class="text-right"><strong><?php echo number_format($qty, 0); ?></strong></td>
                                                                    <td colspan="2"></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    <?php else : ?>
                                                        <div class="text-muted">ไม่พบรายละเอียด Order</div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else : ?>
                            <div class="p-3 text-muted">- ไม่พบข้อมูลประเภทสินค้า -</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 summary-cards">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h5 class="mb-3"><i class="fas fa-chart-bar"></i> สรุปออเดอร์รวม</h5>
                    <div class="row">
                        <div class="col-md-3 col-6 text-center">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h4><?php echo number_format($total_orders); ?></h4>
                                    <p class="mb-0">รายการ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h4><?php echo number_format($total_sum_amount, 0); ?></h4>
                                    <p class="mb-0">แท่ง</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h4><?php echo number_format($total_sum_price / 1000, 0); ?>K</h4>
                                    <p class="mb-0">บาท/กิโล</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h4><?php echo number_format($total_sum_net / 1000, 0); ?>K</h4>
                                    <p class="mb-0">ยอดสุทธิ</p>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div> <!-- /.alert -->
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6 offset-md-6">
                <div class="card text-dark">
                    <div class="card-header">
                        <h5 class="mb-0">สรุปยอดรวม</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6"><strong>จำนวนออเดอร์:</strong></div>
                            <div class="col-6 text-right"><?php echo number_format($total_orders); ?> รายการ</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>รวมจำนวนแท่ง:</strong></div>
                            <div class="col-6 text-right"><?php echo number_format($total_sum_amount, 2); ?> แท่ง</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>รวมบาท/กิโล:</strong></div>
                            <div class="col-6 text-right"><?php echo number_format($total_sum_price, 2); ?> บาท</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>รวมยอดสุทธิ:</strong></div>
                            <div class="col-6 text-right text-success">
                                <strong><?php echo number_format($total_sum_net, 2); ?> บาท</strong>
                            </div>
                        </div>
                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('.product-type-row').on('click', function() {
                    $(this).toggleClass('collapsed');
                });
            });
        </script>
<?php
    }
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setExtraClass("modal-xl");
$modal->setModel("dialog_lock_lookup", "Lock Report");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
?>