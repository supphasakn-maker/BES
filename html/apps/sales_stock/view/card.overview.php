<div class="mb-2">
    <button class="btn btn-outline-dark " onclick="window.history.back()">Back</button>
</div>
<?php
$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'SILVER 1 KG'";
$rst = $dbc->Query($sql);
$line_silver_1kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'SILVER 5 KG'";
$rst = $dbc->Query($sql);
$line_silver_5kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'SILVER 10 KG'";
$rst = $dbc->Query($sql);
$line_silver_10kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'SILVER BAR 1 KG'";
$rst = $dbc->Query($sql);
$line_silver_barkg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ถุงเศษ SILVER'";
$rst = $dbc->Query($sql);
$line_silver_scrap_silver  = $dbc->Fetch($rst);

$total_silver = $line_silver_1kg['weight_expected'] + $line_silver_5kg['weight_expected'] + $line_silver_10kg['weight_expected'] + $line_silver_barkg['weight_expected'] + $line_silver_scrap_silver['weight_expected'];

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'LBMA 1 KG'";
$rst = $dbc->Query($sql);
$line_lbma_1kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'LBMA 5 KG'";
$rst = $dbc->Query($sql);
$line_lbma_5kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'LBMA 10 KG'";
$rst = $dbc->Query($sql);
$line_lbma_10kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ถุงเศษ LBMA'";
$rst = $dbc->Query($sql);
$line_lbma_scrap_silver  = $dbc->Fetch($rst);

$total_lbma = $line_lbma_1kg['weight_expected'] + $line_lbma_5kg['weight_expected'] + $line_lbma_10kg['weight_expected'] + $line_lbma_scrap_silver['weight_expected'];

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'RECYCLE 1 KG'  AND bs_productions.PMR = 'PMR'";
$rst = $dbc->Query($sql);
$line_recyclce_1kg_pmr  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'RECYCLE 1 KG'  AND bs_productions.PMR = 'BWS'";
$rst = $dbc->Query($sql);
$line_recyclce_1kg_bws  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'RECYCLE 5 KG'  AND bs_productions.PMR = 'PMR'";
$rst = $dbc->Query($sql);
$line_recyclce_5kg_pmr  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'RECYCLE 5 KG'  AND bs_productions.PMR = 'BWS'";
$rst = $dbc->Query($sql);
$line_recyclce_5kg_bws  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'RECYCLE 10 KG'  AND bs_productions.PMR = 'PMR'";
$rst = $dbc->Query($sql);
$line_recyclce_10kg_pmr  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'RECYCLE 10 KG'  AND bs_productions.PMR = 'BWS'";
$rst = $dbc->Query($sql);
$line_recyclce_10kg_bws  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ถุงเศษ RECYCLE' ";
$rst = $dbc->Query($sql);
$line_recyclce_scrap  = $dbc->Fetch($rst);

$total_recycle = $line_recyclce_1kg_pmr['weight_expected'] + $line_recyclce_1kg_bws['weight_expected'] + $line_recyclce_5kg_pmr['weight_expected'] + $line_recyclce_5kg_bws['weight_expected'] + $line_recyclce_10kg_pmr['weight_expected'] + $line_recyclce_10kg_bws['weight_expected'] + $line_recyclce_scrap['weight_expected'];



$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ORIGIN THAI 1 KG'";
$rst = $dbc->Query($sql);
$line_thai_1kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ORIGIN THAI 5 KG'";
$rst = $dbc->Query($sql);
$line_thai_5kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ORIGIN THAI 10 KG'";
$rst = $dbc->Query($sql);
$line_thai_10kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ถุงเศษ ORIGIN THAI'";
$rst = $dbc->Query($sql);
$line_thai_scrap  = $dbc->Fetch($rst);

$total_thai = $line_thai_1kg['weight_expected'] + $line_thai_5kg['weight_expected'] + $line_thai_10kg['weight_expected'] + $line_thai_scrap['weight_expected'];


$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'SILVER PLATE 1 แท่ง'";
$rst = $dbc->Query($sql);
$line_silverplate  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'LOCAL RECYCLE 1 KG'";
$rst = $dbc->Query($sql);
$line_local_1kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'LOCAL RECYCLE 5 KG'";
$rst = $dbc->Query($sql);
$line_local_5kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'LOCAL RECYCLE 10 KG'";
$rst = $dbc->Query($sql);
$line_local_10kg  = $dbc->Fetch($rst);

$sql = "SELECT  
COUNT(bs_packing_items.id) AS total_item,
SUM(bs_packing_items.weight_actual) AS weight_actual,
SUM(bs_packing_items.weight_expected) AS weight_expected
FROM bs_packing_items 
LEFT JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id
LEFT JOIN bs_pmr_pack_items ON bs_packing_items.id= bs_pmr_pack_items.item_id
WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status >-1 AND bs_pmr_pack_items.id IS NULL AND bs_packing_items.pack_name = 'ถุงเศษ LOCAL RECYCLE'";
$rst = $dbc->Query($sql);
$line_local_scrap  = $dbc->Fetch($rst);

$total_local = $line_local_1kg['weight_expected'] + $line_local_5kg['weight_expected'] + $line_local_10kg['weight_expected'] + $line_local_scrap['weight_expected'];

?>
<table class="table mt-2 table-bordered">
    <thead>
        <tr>
            <th class="text-center" colspan="3">SILVER</th>
            <th class="text-center" colspan="3">LBMA</th>
            <th class="text-center" colspan="3">RECYCLE</th>
            <th class="text-center" colspan="3">THAI</th>
            <th class="text-center" colspan="3">SILVER PLATE</th>
            <th class="text-center" colspan="3">LOCAL RECYCLE</th>
        </tr>
        <tr>
            <th class="text-center">น้ำหนัก</th>
            <th class="text-center">จำนวนถุง</th>
            <th class="text-center">น้ำหนักคงเหลือ</th>
            <th class="text-center">น้ำหนัก</th>
            <th class="text-center">จำนวนถุง</th>
            <th class="text-center">หมายเลขถุง</th>
            <th class="text-center">น้ำหนัก</th>
            <th class="text-center">จำนวนถุง</th>
            <th class="text-center">หมายเลขถุง</th>
            <th class="text-center">น้ำหนัก</th>
            <th class="text-center">จำนวนถุง</th>
            <th class="text-center">หมายเลขถุง</th>
            <th class="text-center">น้ำหนัก</th>
            <th class="text-center">จำนวนแท่ง</th>
            <th class="text-center">หมายเลขถุง</th>
            <th class="text-center">น้ำหนัก</th>
            <th class="text-center">จำนวนถุง</th>
            <th class="text-center">หมายเลขถุง</th>
        </tr>
    </thead>
    <tbody>
        <?php
        echo '<tr>';
        echo '<td>1 KG</td>';
        echo '<td class="text-center">' . $line_silver_1kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_silver_1kg['weight_expected'], 4) . '</td>';

        echo '<td>1 KG</td>';
        echo '<td class="text-center">' . $line_lbma_1kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_lbma_1kg['weight_expected'], 4) . '</td>';

        echo '<td>1 KG PMR</td>';
        echo '<td class="text-center">' . $line_recyclce_1kg_pmr['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_recyclce_1kg_pmr['weight_expected'], 4) . '</td>';

        echo '<td>1 KG</td>';
        echo '<td class="text-center">' . $line_thai_1kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_thai_1kg['weight_expected'], 4) . '</td>';

        echo '<td>SILVER PLATE 1 แท่ง</td>';
        echo '<td class="text-center">' . $line_silverplate['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_silverplate['weight_expected'], 4) . '</td>';


        echo '<td>1 KG</td>';
        echo '<td class="text-center">' . $line_local_1kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_local_1kg['weight_expected'], 4) . '</td>';


        echo '</tr>';
        echo '<tr>';
        echo '<td>5 KG</td>';
        echo '<td class="text-center">' . $line_silver_5kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_silver_5kg['weight_expected'], 4) . '</td>';

        echo '<td>5 KG</td>';
        echo '<td class="text-center">' . $line_lbma_5kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_lbma_5kg['weight_expected'], 4) . '</td>';

        echo '<td>1 KG BWS</td>';
        echo '<td class="text-center">' . $line_recyclce_1kg_bws['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_recyclce_1kg_bws['weight_expected'], 4) . '</td>';

        echo '<td>5 KG</td>';
        echo '<td class="text-center">' . $line_thai_5kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_thai_5kg['weight_expected'], 4) . '</td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td>5 KG</td>';
        echo '<td class="text-center">' . $line_local_5kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_local_5kg['weight_expected'], 4) . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>10 KG</td>';
        echo '<td class="text-center">' . $line_silver_10kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_silver_10kg['weight_expected'], 4) . '</td>';

        echo '<td>10 KG</td>';
        echo '<td class="text-center">' . $line_lbma_10kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_lbma_10kg['weight_expected'], 4) . '</td>';


        echo '<td>5 KG PMR</td>';
        echo '<td class="text-center">' . $line_recyclce_5kg_pmr['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_recyclce_5kg_pmr['weight_expected'], 4) . '</td>';

        echo '<td>10 KG</td>';
        echo '<td class="text-center">' . $line_thai_10kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_thai_10kg['weight_expected'], 4) . '</td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td>10 KG</td>';
        echo '<td class="text-center">' . $line_local_10kg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_local_10kg['weight_expected'], 4) . '</td>';

        echo '</tr>';
        echo '<tr>';
        echo '<td>SILVER BAR 1 KG</td>';
        echo '<td class="text-center">' . $line_silver_barkg['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_silver_barkg['weight_expected'], 4) . '</td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td>5 KG BWS</td>';
        echo '<td class="text-center">' . $line_recyclce_5kg_bws['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_recyclce_5kg_bws['weight_expected'], 4) . '</td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>ถุงเศษ SILVER</td>';
        echo '<td class="text-center">' . $line_silver_scrap_silver['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_silver_scrap_silver['weight_expected'], 4) . '</td>';
        echo '<td>ถุงเศษ LBMA</td>';
        echo '<td class="text-center">' . $line_lbma_scrap_silver['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_lbma_scrap_silver['weight_expected'], 4) . '</td>';

        echo '<td>10 KG PMR</td>';
        echo '<td class="text-center">' . $line_recyclce_10kg_pmr['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_recyclce_10kg_pmr['weight_expected'], 4) . '</td>';
        echo '<td>ถุงเศษ THAI </td>';
        echo '<td class="text-center">' . $line_thai_scrap['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_thai_scrap['weight_expected'], 4) . '</td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td>ถุงเศษ LOCAL </td>';
        echo '<td class="text-center">' . $line_local_scrap['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_local_scrap['weight_expected'], 4) . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';

        echo '<td>10 KG BWS</td>';
        echo '<td class="text-center">' . $line_recyclce_10kg_bws['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_recyclce_10kg_bws['weight_expected'], 4) . '</td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';

        echo '<td>ถุงเศษ RECYCLE</td>';
        echo '<td class="text-center">' . $line_recyclce_scrap['total_item'] . '</td>';
        echo '<td class="text-center">' . number_format($line_recyclce_scrap['weight_expected'], 4) . '</td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '<td></td>';
        echo '<td class="text-center"></td>';
        echo '<td class="text-center"></td>';
        echo '</tr>';

        ?>
    </tbody>
    <tfoot>
        <?php

        echo '<tr>';
        echo '<td class="text-center" colspan="2">รวม SILVER</td>';
        echo '<td class="text-center">' . number_format($total_silver, 4) . '</td>';

        echo '<td class="text-center" colspan="2">รวม LBMA</td>';
        echo '<td class="text-center">' . number_format($total_lbma, 4) . '</td>';

        echo '<td class="text-center" colspan="2">รวม RECYCLE</td>';
        echo '<td class="text-center">' . number_format($total_recycle, 4) . '</td>';

        echo '<td class="text-center" colspan="2">รวม THAI</td>';
        echo '<td class="text-center">' . number_format($total_thai, 4) . '</td>';

        echo '<td class="text-center" colspan="2">รวม SILVER PLATE </td>';
        echo '<td class="text-center">' . number_format($line_silverplate['weight_expected'], 4) . '</td>';
        echo '</tr>';
        ?>
    </tfoot>
</table>