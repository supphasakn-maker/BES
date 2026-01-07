<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../include/const_jinsung.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$date = strtotime($_POST['date']);
$date_for_sql = $_POST['date'];
$newDate = date("Y-m-d", strtotime("-1 days", strtotime($date_for_sql)));

$order_condition = " bs_orders.parent IS NULL AND bs_orders.status > -1";

$sql = "SELECT
		COUNT(id) AS total_item, 
		SUM(amount) AS amount, 
		SUM(total) AS total,
		SUM(net) AS net
	FROM bs_orders 
	WHERE 
		YEAR(date) > 2021
		AND DATE(date) <= '$date_for_sql'
		AND bs_orders.parent IS NULL 
		AND bs_orders.status > -1";
$rst = $dbc->Query($sql);
$order = $dbc->Fetch($rst);

$sql = "SELECT 
		COUNT(id) AS total_item, 
		SUM(amount) AS amount, 
		SUM((rate_spot+rate_pmdc)*amount*32.1507) AS total
	FROM bs_purchase_spot 
	WHERE
		YEAR(date) > 2021
		AND DATE(date) <= '$date_for_sql'
		AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') 
		AND rate_spot > 0 AND status > 0 AND currency != 'THB'";
$rst = $dbc->Query($sql);
$purchase = $dbc->Fetch($rst);

$sql = "SELECT 
		COUNT(id) AS total_item, 
		SUM(amount) AS amount
	FROM bs_purchase_usd 
	WHERE 
		YEAR(date) > 2021
		AND DATE(date) <= '$date_for_sql'
		AND parent IS NULL AND status != 0";
$rst = $dbc->Query($sql);
$purchase_usd = $dbc->Fetch($rst);

$balance_amount = -$order['amount'] + $purchase['amount'] - 19;
$balance_usd = -$purchase['total'] + $purchase_usd['amount'] - 4710.36;

echo $balance_amount;
echo "|";
echo $balance_usd;

function replace_sql_string($text, $product_id = 0, $supplier_id = 0)
{
    $text = str_replace("#sql_date#", $_POST['date'], $text);
    $text = str_replace("#sql_date2#", date("Y-m-d", strtotime("-1 days", strtotime($_POST['date']))), $text);
    $text = str_replace("#product_id#", $product_id, $text);
    $text = str_replace("#supplier_id#", $supplier_id, $text);
    return $text;
}

$aSupplier = array();
$aSum = array();
$aSumPending = array();
$aSumPurchase = array();
$aSumUntake = array();
$TotalOnHand = 0;
$TotalFinal = 0;

// ดึงข้อมูลจาก DB โดยอัตโนมัติ เรียง Stone-X ไว้หน้าสุด
$sql = "SELECT product_id, supplier_id, name 
        FROM bs_suppliers_jinsung 
        ORDER BY CASE 
            WHEN name LIKE 'Stone%' THEN 1
            ELSE 2 
        END, id";
$rst = $dbc->Query($sql);
$suppliers_data = array();
while ($row = $dbc->Fetch($rst)) {
    $suppliers_data[] = array(
        "product_id" => $row['product_id'],
        "supplier_id" => $row['supplier_id'],
        "name" => $row['name']
    );
}

foreach ($suppliers_data as $supplier) {
    array_push($aSupplier, $supplier);
    array_push($aSum, array(0, 0));
    array_push($aSumPurchase, array(0, 0));
    array_push($aSumPending, array(0, 0));
    array_push($aSumUntake, array(0, 0));
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report - <?php echo $_POST['date']; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Base styles */
        .report-container {
            padding: 10px;
            font-size: 12px;
            background-color: #ffffff;
        }

        .table-responsive-custom {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 32, 78, 0.1);
        }

        .financial-table {
            min-width: 800px;
            margin-bottom: 0;
            background-color: #ffffff;
        }

        .financial-table th,
        .financial-table td {
            white-space: nowrap;
            padding: 8px 6px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        /* Header gradient สี #00204E แบบตัวอย่าง */
        .header-gradient {
            background: linear-gradient(135deg, #00204E, #003366) !important;
        }

        .blue-gradient {
            background: linear-gradient(135deg, #00204E, #004080, #0066CC) !important;
        }

        /* Mobile styles */
        @media (max-width: 767px) {
            .report-container {
                padding: 5px;
                font-size: 10px;
            }

            .financial-table {
                min-width: 600px;
            }

            .financial-table th,
            .financial-table td {
                padding: 4px 3px;
                font-size: 10px;
            }

            .supplier-name {
                max-width: 60px;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .row-header {
                max-width: 80px;
                font-size: 9px;
                line-height: 1.2;
            }
        }

        /* iPad styles */
        @media (min-width: 768px) and (max-width: 1024px) {
            .report-container {
                padding: 15px;
                font-size: 11px;
            }

            .financial-table {
                min-width: 700px;
            }

            .financial-table th,
            .financial-table td {
                padding: 6px 4px;
                font-size: 11px;
            }
        }

        /* Desktop styles */
        @media (min-width: 1025px) {
            .report-container {
                padding: 20px;
                font-size: 13px;
            }
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* Sticky header for mobile */
        @media (max-width: 767px) {
            .table-responsive-custom {
                position: relative;
            }

            .financial-table thead th {
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .financial-table tbody th:first-child {
                position: sticky;
                left: 0;
                z-index: 9;
                background-color: #f8f9fa;
                border-right: 2px solid #dee2e6;
            }
        }

        /* Touch-friendly scrollbar */
        .table-responsive-custom::-webkit-scrollbar {
            height: 12px;
        }

        .table-responsive-custom::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }

        .table-responsive-custom::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 6px;
        }

        .table-responsive-custom::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body>

    <div class="container-fluid report-container">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive-custom">
                    <table class="table table-sm table-bordered financial-table">
                        <thead style="background: linear-gradient(135deg, #00204E, #003366);">
                            <tr>
                                <th class="text-center text-white font-weight-bold" rowspan="2">
                                    <?php echo $_POST['date']; ?>
                                </th>
                                <?php foreach ($aSupplier as $supplier): ?>
                                    <th class="text-center text-white font-weight-bold supplier-name" colspan="2">
                                        <?php echo $supplier['name']; ?>
                                    </th>
                                <?php endforeach; ?>
                                <th class="text-center text-white font-weight-bold" colspan="2">Total</th>
                            </tr>
                            <tr>
                                <?php foreach ($aSupplier as $supplier): ?>
                                    <th class="text-center text-white font-weight-bold">KG</th>
                                    <th class="text-center text-white font-weight-bold">USD</th>
                                <?php endforeach; ?>
                                <th class="text-center text-white font-weight-bold">KG</th>
                                <th class="text-center text-white font-weight-bold">USD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aContent as $key => $content): ?>
                                <?php
                                $eachTotalPurchase = array(0, 0, 0);
                                $eachTotal = array(0, 0);
                                $isContent = is_array($content);
                                ?>
                                <tr>
                                    <th class="text-left font-weight row-header">
                                        <?php echo ($isContent ? $content[1] : $content); ?>
                                    </th>

                                    <?php foreach ($aSupplier as $key_sup => $supplier): ?>
                                        <?php if ($isContent && $content[0] == "supplier"): ?>
                                            <?php
                                            // KG Query
                                            $rst = $dbc->Query(replace_sql_string($content[2], $supplier['product_id'], $supplier['supplier_id']));
                                            $line = $dbc->Fetch($rst);
                                            $kg_value = $line[0] ?? '0';
                                            ?>
                                            <td class="text-right"><?php echo $kg_value; ?></td>

                                            <?php
                                            if (in_array($key, array(0, 1, 2))) {
                                                $aSum[$key_sup][0] += floatval(str_replace(",", "", $kg_value));
                                            } else if (in_array($key, array(4, 5, 6, 7))) {
                                                $aSum[$key_sup][0] += floatval(str_replace(",", "", $kg_value));
                                                $aSumPending[$key_sup][0] += floatval(str_replace(",", "", $kg_value));
                                            }
                                            $eachTotalPurchase[0] += floatval(str_replace(",", "", $kg_value));

                                            // USD Query
                                            $rst = $dbc->Query(replace_sql_string($content[3], $supplier['product_id'], $supplier['supplier_id']));
                                            $line = $dbc->Fetch($rst);
                                            $usd_value = $line[0] ?? '0';
                                            ?>
                                            <td class="text-right"><?php echo number_format((float)$usd_value, 2); ?></td>

                                            <?php
                                            if (in_array($key, array(0, 1, 2))) {
                                                $aSum[$key_sup][1] += floatval(str_replace(",", "", $usd_value));
                                            } else if (in_array($key, array(4, 5, 6, 7))) {
                                                $aSum[$key_sup][1] += floatval(str_replace(",", "", $usd_value));
                                                $aSumPending[$key_sup][1] += floatval(str_replace(",", "", $usd_value));
                                            }
                                            $eachTotalPurchase[1] += floatval(str_replace(",", "", $usd_value));
                                            ?>

                                        <?php elseif ($key == 3): ?>
                                            <td class="text-right text-white font-weight-bold" style="background: linear-gradient(135deg, #00204E, #004080, #0066CC);">
                                                <?php echo number_format($aSum[$key_sup][0], 4); ?>
                                            </td>
                                            <td class="text-right text-white font-weight-bold" style="background: linear-gradient(135deg, #00204E, #004080, #0066CC);">
                                                <?php echo number_format($aSum[$key_sup][1], 2); ?>
                                            </td>
                                            <?php
                                            $aSumPurchase[$key_sup][0] += $aSum[$key_sup][0];
                                            $aSumPurchase[$key_sup][1] += $aSum[$key_sup][1];
                                            $eachTotalPurchase[0] += $aSum[$key_sup][0];
                                            $eachTotalPurchase[1] += $aSum[$key_sup][1];
                                            ?>

                                        <?php elseif (in_array($key, array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 29, 30, 31, 32, 33, 34, 35))): ?>
                                            <td class="bg-dark"></td>
                                            <td class="bg-dark"></td>

                                        <?php else: ?>
                                            <td class="text-center"></td>
                                            <td class="text-center">x</td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <?php if ($isContent && $content[0] == "total"): ?>
                                        <?php if ($key == 63): ?>
                                            <td class="text-right text-primary">
                                                <?php echo number_format($TotalOnHand, 4); ?>
                                            </td>
                                            <td class="text-center text-danger"><?php echo $key; ?></td>
                                            <?php $TotalFinal = $TotalOnHand; ?>

                                        <?php elseif ($key == 53): ?>
                                            <td class="text-right text-primary">
                                                <?php echo number_format($TotalFinal, 4); ?>
                                            </td>
                                            <td class="text-center text-danger"><?php echo $key; ?></td>

                                        <?php else: ?>
                                            <?php
                                            $rst = $dbc->Query(replace_sql_string($content[2]));
                                            $line = $dbc->Fetch($rst);
                                            if (is_null($line)) {
                                                echo '<td class="text-center">-</td>';
                                            } else if ($key == 12) {
                                                echo '<th class="text-right bg-primary text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 13) {
                                                echo '<th class="text-right bg-secondary text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 14) {
                                                echo '<th class="text-right bg-success text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 15) {
                                                echo '<th class="text-right bg-success text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 16) {
                                                echo '<th class="text-right bg-warning text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 17) {
                                                echo '<th class="text-right text-white font-weight-bold" style="background-color: #8B4513;">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 18) {
                                                echo '<th class="text-right text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 19) {
                                                echo '<th class="text-right bg-primary text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 20) {
                                                echo '<th class="text-right bg-secondary text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 21) {
                                                echo '<th class="text-right bg-success text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 22) {
                                                echo '<th class="text-right text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
                                            } else if ($key == 23) {
                                                echo '<th class="text-right text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
                                            } else {
                                                echo '<th class="text-right font-weight-bold">' . number_format($line[0], 4) . '</th>';
                                                if (in_array($key, array(19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32))) {
                                                    $TotalOnHand = $TotalOnHand + floatval($line[0]);
                                                } else if ($key == 62) { // - Total Stock
                                                    $TotalOnHand -= floatval($line[0]);
                                                } else if ($key == 32) { // - Sigmagin
                                                    $TotalOnHand -= floatval($line[0]);
                                                } else if ($key == 33) { //R38
                                                    $TotalOnHand += floatval($line[0]);
                                                } else if ($key == 34) { //R38
                                                    $TotalOnHand += floatval($line[0]);
                                                } else if ($key == 35) { //Physical
                                                    $TotalOnHand -= floatval($line[0]);
                                                } else if ($key == 38) { //Fixed Stock
                                                    $TotalOnHand -= floatval($line[0]);
                                                } else if ($key == 66) { //Fixed Stock
                                                    $TotalFinal += floatval($line[0]);
                                                } else if ($key == 50) { //Fixed Stock
                                                    $TotalFinal += floatval($line[0]);
                                                }
                                            }

                                            $rst = $dbc->Query(replace_sql_string($content[3]));
                                            $line = $dbc->Fetch($rst);
                                            echo '<th class="text-center text-danger font-weight-bold">' . ($line[0] ?? '0') . '</th>';
                                            ?>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <td class="text-right text-danger font-weight-bold">
                                            <?php echo number_format($eachTotalPurchase[0], 4); ?>
                                        </td>
                                        <td class="text-right text-danger font-weight-bold">
                                            <?php echo number_format($eachTotalPurchase[1], 2); ?>
                                        </td>
                                        <?php
                                        if ($key == 21) {
                                            $TotalOnHand -= $eachTotalPurchase[0];
                                        }
                                        ?>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Touch and swipe improvements for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const tableContainer = document.querySelector('.table-responsive-custom');

            if (tableContainer) {
                let isScrolling = false;
                let startX = 0;
                let scrollLeft = 0;

                // Mouse events
                tableContainer.addEventListener('mousedown', (e) => {
                    isScrolling = true;
                    startX = e.pageX - tableContainer.offsetLeft;
                    scrollLeft = tableContainer.scrollLeft;
                    tableContainer.style.cursor = 'grabbing';
                });

                tableContainer.addEventListener('mouseleave', () => {
                    isScrolling = false;
                    tableContainer.style.cursor = 'default';
                });

                tableContainer.addEventListener('mouseup', () => {
                    isScrolling = false;
                    tableContainer.style.cursor = 'default';
                });

                tableContainer.addEventListener('mousemove', (e) => {
                    if (!isScrolling) return;
                    e.preventDefault();
                    const x = e.pageX - tableContainer.offsetLeft;
                    const walk = (x - startX) * 2;
                    tableContainer.scrollLeft = scrollLeft - walk;
                });

                // Touch events for mobile
                let touchStartX = 0;
                let touchScrollLeft = 0;

                tableContainer.addEventListener('touchstart', (e) => {
                    touchStartX = e.touches[0].pageX;
                    touchScrollLeft = tableContainer.scrollLeft;
                });

                tableContainer.addEventListener('touchmove', (e) => {
                    const touchX = e.touches[0].pageX;
                    const walk = touchStartX - touchX;
                    tableContainer.scrollLeft = touchScrollLeft + walk;
                });
            }
        });
    </script>

</body>

</html>

<?php
$dbc->Close();
?>