<?php
session_start();
include_once "../../../../config/define.php";
include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

include "../../include/const.php";

$title = $aReportType[$_POST['type']];
$subtitle = "ประดือน " . date("F Y", strtotime($_POST['month']));


?>
<section class="text-center">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $subtitle; ?> </p>
</section>
<div class="overflow-auto">
    <table class="table table-sm table-bordered table-striped overflow-auto">
        <thead>
            <tr>
                <th class="text-center">ลำดับ</th>
                <th class="text-center">เลขที่ ORDER</th>
                <th class="text-center">ลูกค้า</th>
                <th class="text-center">วันที่ซื้อ</th>
                <th class="text-center">วันที่ส่งของ</th>
                <th class="text-center">Dif</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT id ,code, date, customer_name, delivery_date, DATEDIFF(DATE(delivery_date), DATE(date)) AS day_difference 
                    FROM `bs_orders` 
                    WHERE bs_orders.parent IS NULL 
                    AND bs_orders.status > -1 
                    AND DATE_FORMAT(bs_orders.delivery_date,'%Y-%m') = '" . $_POST['month'] . "' 
                    AND DATEDIFF(DATE(delivery_date), DATE(date)) > 7 
                    ORDER BY customer_name, day_difference DESC"; // เรียงตามลูกค้าและ day_difference
            $rst = $dbc->Query($sql);
            $customers = array();
            while ($set = $dbc->Fetch($rst)) {
                $customer_name = $set['customer_name'];
                if (!isset($customers[$customer_name])) {
                    $customers[$customer_name] = array(
                        'orders' => array(),
                        'total_day_difference' => 0,
                        'order_count' => 0,
                        'average_day_difference' => 0
                    );
                }
                $customers[$customer_name]['orders'][] = $set;
                $customers[$customer_name]['total_day_difference'] += $set['day_difference'];
                $customers[$customer_name]['order_count']++;
                $customers[$customer_name]['average_day_difference'] = ($customers[$customer_name]['order_count'] > 0) ? round($customers[$customer_name]['total_day_difference'] / $customers[$customer_name]['order_count'], 2) : 0;
            }

            // Sort the customers array by average day difference in descending order.
            uasort($customers, function ($a, $b) {
                return $b['average_day_difference'] <=> $a['average_day_difference'];
            });

            $number = 1;
            foreach ($customers as $customer_name => $customer_data) {
                echo '<tr>';
                echo '<td class="text-center">' . $number . '</td>';
                echo '<td colspan="4" class="text-left"><b>ลูกค้า: ' . $customer_name . '</b></td>'; // แสดงชื่อลูกค้า
                echo '</tr>';
                $order_number = 1;
                foreach ($customer_data['orders'] as $order) {
                    $day_difference = $order['day_difference'];
                    $color = ($day_difference > 7) ? 'red' : '';
                    echo '<tr>';
                    echo '<td></td>'; // ช่องว่างสำหรับเยื้อง
                    echo '<td class="text-center">' . $order_number . '. ' . $order['code'] . '</td>'; // แสดงเลขที่ order
                    echo '<td></td>'; // ช่องว่าง
                    echo '<td class="text-left">' . $order['date'] . '</td>';
                    echo '<td class="text-left">' . $order['delivery_date'] . '</td>';
                    echo '<td class="text-right" style="color: ' . $color . '">' . $day_difference . ' วัน</td>';
                    echo '</tr>';
                    $order_number++;
                }
                echo '<tr>';
                echo '<td></td>';
                echo '<td colspan="4" class="text-right"><b>เฉลี่ย ' . $customer_name . ':</b></td>';
                echo '<td class="text-right"><b>' .  $customer_data['average_day_difference'] . '</b></td>'; // แสดงผลรวม
                echo '</tr>';
                $number++;
            }


            ?>

        </tbody>

    </table>
</div>
<?php
$dbc->Close();
?>