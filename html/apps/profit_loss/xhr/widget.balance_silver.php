<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);



$balance = -739;

$sql = "SELECT date FROM ((SELECT DATE(date) AS date FROM bs_orders) 
    UNION (SELECT date AS date FROM bs_purchase_spot)) AS tmp 
    WHERE DATE(date) > '2023-09-29' ";
$sql .= "GROUP BY(date) ORDER BY(date) ASC;";
$rst = $dbc->Query($sql);
while ($record = $dbc->Fetch($rst)) {
    $order = array();
    $purchase = array();
    $purchase_usd = array();
    $sale_price = 0;
    $sale_margin = 0;
    $pur = 0;
    $sale = 0;
    $margin = 0;
    $sql = "SELECT * FROM bs_orders WHERE DATE(date) = '" . $record['date'] . "' AND " . $order_condition;
    $rst_order = $dbc->Query($sql);
    while ($item = $dbc->Fetch($rst_order)) {
        array_push($order, array(
            $item['code'],
            $item['amount'],
            $item['price'],
            $item['total']
        ));
        $sale -= $item['amount'];
        $margin -= $item['amount'];
        $sale_price += $item['net'];
    }

    $sql = "SELECT * FROM bs_purchase_spot WHERE date = '" . $record['date'] . "' 
				AND (bs_purchase_spot.type LIKE 'physical'
				OR bs_purchase_spot.type LIKE 'stock'
				)AND rate_spot > 0 AND status > -1  AND confirm IS NOT NULL";
    $rst_purchase = $dbc->Query($sql);
    while ($item = $dbc->Fetch($rst_purchase)) {
        if (!$dbc->HasRecord("bs_purchase_spot", "parent=" . $item['id'])) {
            $total = ($item['rate_spot'] + $item['rate_pmdc']) * $item['amount'] * 32.1507;
            $usd = $item['rate_spot'];
            $aa = $item['rate_spot'] + $item['rate_pmdc'];
            array_push($purchase, array(
                $item['amount'],
                $item['rate_spot'],
                $item['rate_pmdc'],
                number_format($total, 2)
            ));
            array_push($purchase_usd, array(
                $item['rate_spot']
            ));
            $margin += $item['amount'];
        }
    }

    $sql = "";
    $pur += ($sale) * ($sale_margin) * 32.1507;
    $balance += $margin;
}


?>


<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="trending-up" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($balance, 4); ?></h3>
            <span id="yourPhotos"><?php echo number_format($usd, 4); ?> </span>
        </div>
        <h6 class="text-success">Balance Silver</h6>
        <p class="small text-secondary mb-0">Balance Silver ล่าสุด</p>
    </div>
</div>